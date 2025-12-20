<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration - using Open Trivia DB instead
const API_BASE_URL = 'https://opentdb.com';

/**
 * Makes a request to the Open Trivia DB API
 */
function makeApiRequest($endpoint) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'Connection error: ' . $error
        ];
    }
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'error' => 'API returned status code: ' . $httpCode
        ];
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Invalid JSON response from API'
        ];
    }
    
    return [
        'success' => true,
        'data' => $data
    ];
}

/**
 * Decodes HTML entities from Open Trivia DB responses
 */
function decodeText($text) {
    return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validates and processes the Open Trivia DB API response
 */
function processApiResponse($apiResponse) {
    if (!$apiResponse['success']) {
        return [
            'success' => false,
            'error' => $apiResponse['error']
        ];
    }
    
    $data = $apiResponse['data'];
    
    // Check response code from Open Trivia DB
    if (isset($data['response_code']) && $data['response_code'] !== 0) {
        $errorMessages = [
            1 => 'No results found',
            2 => 'Invalid parameter',
            3 => 'Token not found',
            4 => 'Token empty',
            5 => 'Rate limit exceeded'
        ];
        
        $code = $data['response_code'];
        $message = $errorMessages[$code] ?? 'Unknown error';
        
        return [
            'success' => false,
            'error' => $message
        ];
    }
    
    // Check if we have results
    if (!isset($data['results']) || empty($data['results'])) {
        return [
            'success' => false,
            'error' => 'No questions returned from API'
        ];
    }
    
    return [
        'success' => true,
        'data' => $data['results']
    ];
}

// Route handler
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'random-clue':
        // Open Trivia DB endpoint - get 1 random question
        $apiResponse = makeApiRequest('/api.php?amount=1');
        $result = processApiResponse($apiResponse);
        
        if ($result['success']) {
            // Extract the first question from the results array
            $question = $result['data'][0] ?? null;
            
            if (!$question) {
                echo json_encode([
                    'success' => false,
                    'error' => 'No question data available'
                ]);
                exit;
            }
            
            // Open Trivia DB format: question, correct_answer, incorrect_answers, category, etc.
            // Normalize to our expected format (similar to Jeopardy style)
            $normalizedClue = [
                'category' => decodeText($question['category'] ?? 'General Knowledge'),
                'clue' => decodeText($question['question'] ?? ''),
                'response' => decodeText($question['correct_answer'] ?? ''),
                'difficulty' => $question['difficulty'] ?? 'medium',
                'type' => $question['type'] ?? 'multiple'
            ];
            
            // Validate required fields
            if (empty($normalizedClue['clue']) || empty($normalizedClue['response'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid question data'
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'clue' => $normalizedClue
            ]);
        } else {
            http_response_code(500);
            echo json_encode($result);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid endpoint'
        ]);
        break;
}