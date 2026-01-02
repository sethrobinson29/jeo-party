<?php

namespace Services;

/**
 * Open Trivia DB Service
 * Handles communication with the Open Trivia Database API
 */
class OpenTriviaService
{
    private const API_BASE_URL = 'https://opentdb.com';

    /**
     * Fetch a random question from Open Trivia DB
     *
     * @return array Result with 'success' and 'data' or 'error'
     */
    public function fetchRandomQuestion()
    {
        $endpoint = '/api.php?amount=1';
        return $this->makeRequest($endpoint);
    }

    /**
     * Make a request to the Open Trivia DB API
     *
     * @param string $endpoint API endpoint path
     * @return array Result with 'success' and 'data' or 'error'
     */
    private function makeRequest($endpoint)
    {
        $url = self::API_BASE_URL . $endpoint;

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

        return $this->processResponse($data);
    }

    /**
     * Process and validate the API response
     *
     * @param array $data Raw API response
     * @return array Processed result with 'success' and 'data' or 'error'
     */
    private function processResponse($data)
    {
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
}