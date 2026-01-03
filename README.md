Jeopardy Trivia Game
A full-stack trivia game application built with React and PHP 8.2.27, featuring Jeopardy-style questions from the Open Trivia Database. Built with modern MVC architecture and extensible service layer.

Features
🎮 Interactive trivia gameplay with random questions
✅ Real-time input validation (letters and numbers only)
🎨 Beautiful Jeopardy-themed UI with animations
⌨️ Keyboard support (press Enter to submit)
🔄 Quick "Next Question" functionality
📱 Responsive design
🔌 Pluggable trivia service architecture
🛡️ Request validation and security layer
Tech Stack
Frontend:

React 18.2
Tailwind CSS 3.4
Modern hooks-based architecture
Backend:

PHP 8.2.27
MVC Architecture
Service-oriented design
RESTful API
PSR-4 autoloading
Strict type declarations
External API:

Open Trivia Database - Free trivia questions API
Project Structure
trivia/
├── backend/
│   ├── index.php                          # Entry point - request validation
│   ├── router.php                         # Router for PHP built-in server
│   ├── .htaccess                          # Apache configuration
│   ├── config/
│   │   └── routes.php                     # Route definitions
│   └── src/
│       ├── Core/
│       │   ├── Application.php            # Application bootstrap
│       │   ├── Request.php                # Request handling & validation
│       │   ├── Response.php               # Response formatting
│       │   └── Router.php                 # Route matching & dispatch
│       ├── Controllers/
│       │   ├── BaseController.php         # Base controller with utilities
│       │   └── ClueController.php         # Trivia clue endpoints
│       └── Services/
│           ├── TriviaServiceInterface.php # Service contract
│           ├── BaseTriviaService.php      # Shared service functionality
│           ├── OpenTriviaService.php      # Open Trivia DB implementation
│           └── CustomTriviaService.php    # Example custom service
├── public/
│   └── index.html
├── src/
│   ├── components/
│   │   ├── ClueCard.jsx                   # Question display and answer input
│   │   ├── ResultCard.jsx                 # Correct/incorrect feedback
│   │   ├── StartScreen.jsx                # Initial screen with start button
│   │   └── ErrorMessage.jsx               # Error display component
│   ├── services/
│   │   └── api.js                         # API service layer
│   ├── App.jsx                            # Main application component
│   ├── index.js                           # React entry point
│   └── index.css                          # Global styles with Tailwind
├── package.json
├── tailwind.config.js
└── postcss.config.js
Prerequisites
Node.js 14+ and npm
PHP 8.2.27+ with cURL extension enabled
Modern web browser
Installation
1. Clone the repository
   bash
   git clone <your-repo-url>
   cd trivia
2. Install frontend dependencies
   bash
   npm install
3. Backend setup
   The backend is already configured and ready to use. No additional setup required.

Running the Application
You need to run two servers simultaneously:

Terminal 1: PHP Backend (Port 8000)
bash
cd backend
php -S localhost:8000 router.php
Note: Use router.php when running PHP's built-in server. If deploying to Apache, the .htaccess file handles routing automatically.

Terminal 2: React Frontend (Port 3000)
bash
npm start
This starts the React development server with hot reloading.

Access the Application
Open your browser to:

http://localhost:3000
How to Play
Click "Get Random Clue" to fetch a trivia question
Read the category and question
Type your answer in the input field (letters and numbers only)
Press Enter or click "Submit Answer"
See if you got it right with animated feedback
Click "Next Question" to continue or "Back to Start" to reset
API Documentation
Backend Architecture
The backend follows MVC architecture with a service layer:

Entry Point (index.php) - Validates requests for security
Router - Matches URLs to controller actions
Controllers - Handle HTTP requests and responses
Services - Interface with external APIs or databases
Models - (Future: Business logic and data transformation)
API Endpoints
Base URL: http://localhost:8000/api

GET /clues/random
Fetch a random trivia clue.

Response:

json
{
"success": true,
"data": {
"clue": {
"category": "Science",
"clue": "This is the chemical symbol for gold",
"response": "Au",
"difficulty": "easy",
"type": "multiple",
"source": "Open Trivia Database"
}
}
}
Future Endpoints
GET  /clues/category/:category    - Get clues by category
GET  /clues/difficulty/:difficulty - Get clues by difficulty
POST /clues/validate               - Validate an answer
Extending the Backend
Adding a New Trivia Service
The application uses a service-oriented architecture. To add a new trivia API:

1. Create a new service class
   php
<?php
namespace App\Services;

class JServiceTriviaService extends BaseTriviaService
{
    private const API_BASE_URL = 'https://jservice.io';
    
    public function getRandomClue(): array
    {
        $response = $this->makeHttpRequest(
            self::API_BASE_URL . '/api/random'
        );
        return $this->normalizeClue($response[0]);
    }
    
    protected function normalizeClue(array $rawData): array
    {
        return [
            'category' => $rawData['category']['title'] ?? 'Unknown',
            'clue' => $rawData['question'] ?? '',
            'response' => $rawData['answer'] ?? '',
            'difficulty' => 'medium',
            'type' => 'jeopardy',
            'source' => 'jService'
        ];
    }
    
    // Implement other interface methods...
}
2. Update the controller
In src/Controllers/ClueController.php:

php
public function __construct()
{
    // Switch to your new service
    $this->triviaService = new JServiceTriviaService();
}
That's it! The rest of the application continues to work unchanged.

Adding New Routes
Edit config/routes.php:

php
'GET' => [
    '/api/clues/random' => [ClueController::class, 'random'],
    '/api/clues/category/:category' => [ClueController::class, 'byCategory'],
],
Then add the corresponding method in your controller:

php
public function byCategory(Request $request): Response
{
    $category = $this->getRouteParam($request, 'category');
    // Handle request...
}
Configuration
Change Trivia Service
Edit src/Controllers/ClueController.php constructor to switch services:

php
$this->triviaService = new OpenTriviaService();    // Default
$this->triviaService = new CustomTriviaService();  // Your custom API
Frontend API Base URL
Edit src/services/api.js:

javascript
const API_BASE = 'http://localhost:8000/api/clues';
Validation Rules
Edit validateInput in src/App.jsx:

javascript
const validateInput = (value) => {
  const regex = /^[a-zA-Z0-9\s]*$/;
  return regex.test(value);
};
Security Features
The backend includes several security measures:

✅ Request validation (XSS, SQL injection, path traversal detection)
✅ HTTP method validation
✅ URI length limits
✅ Security headers (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection)
✅ Directory listing disabled
✅ Sensitive file access blocked
✅ Rate limiting hooks (ready for Redis/Memcached)
Development
Available Scripts
Frontend:

npm start - Start development server
npm run build - Build for production
npm test - Run tests
npm run eject - Eject from Create React App
Backend:

php -S localhost:8000 router.php - Start development server
Code Standards
PHP:

Strict types enabled (declare(strict_types=1))
Type hints on all parameters and return types
PSR-4 autoloading
Namespaces for organization
React:

Functional components with Hooks
Props validation through TypeScript-style comments
Clean component separation
Production Deployment
Build the React App
bash
npm run build
This creates optimized production files in the build/ folder.

Deploy Backend
Apache Server
Upload the backend/ folder to your web host
Ensure .htaccess is enabled (AllowOverride All)
The .htaccess file handles routing automatically
Update CORS headers in index.php if needed
Nginx
Add this to your Nginx configuration:

nginx
location /api {
    try_files $uri $uri/ /index.php?$query_string;
}
Requirements
PHP 8.2.27 or higher
cURL extension enabled
mod_rewrite enabled (Apache) or equivalent
Deploy Frontend
Upload the build/ folder contents to your web host, or use:

Vercel
Netlify
GitHub Pages
AWS S3 + CloudFront
Important: Update API_BASE in production to point to your hosted backend URL.

Troubleshooting
Port Already in Use
bash
# For React (use different port)
PORT=3001 npm start

# For PHP (use different port)
php -S localhost:8001 router.php
Update the frontend API base URL if you change the PHP port.

SSL Certificate Errors
For local development, SSL verification is disabled in services. For production:

php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
Route Not Found (404)
With PHP built-in server: Use php -S localhost:8000 router.php
With Apache: Ensure .htaccess is being read (AllowOverride All)
Check that routes are defined in config/routes.php
Cache Issues
bash
# Clear React cache
rm -rf node_modules/.cache
npm start
PhpStorm File Hiding
PhpStorm may hide duplicate files (like App.js inside App.jsx). Check for and delete any duplicate files.

CORS Errors
Update CORS headers in backend/index.php:

php
header('Access-Control-Allow-Origin: https://yourdomain.com');
Testing
Backend API Testing
bash
# Test random clue endpoint
curl http://localhost:8000/api/clues/random

# Test with verbose output
curl -v http://localhost:8000/api/clues/random
Frontend Testing
bash
npm test
Future Enhancements
 Score tracking with persistent storage
 Difficulty selection
 Category filtering
 Timed questions mode
 Multiplayer support
 Leaderboard
 Multiple choice options display
 Answer history
 User authentication
 Custom question sets
 Admin panel for question management
 Database integration for custom questions
 Redis rate limiting
 Caching layer
 Dependency injection container
Architecture Benefits
✅ Separation of Concerns - Clear boundaries between layers
✅ Extensible - Easy to add new APIs or features
✅ Testable - Services and controllers can be unit tested
✅ Type Safe - PHP 8.2 strict types prevent bugs
✅ Secure - Multiple validation layers
✅ RESTful - Standard HTTP methods and URLs
✅ Maintainable - Clear structure and naming conventions

Contributing
Fork the repository
Create a feature branch (git checkout -b feature/amazing-feature)
Follow existing code style (PSR-12 for PHP, Airbnb for JavaScript)
Write tests if applicable
Commit your changes (git commit -m 'Add amazing feature')
Push to the branch (git push origin feature/amazing-feature)
Open a Pull Request
License
This project is open source and available under the MIT License.

Acknowledgments
Trivia questions provided by Open Trivia Database
Inspired by the classic Jeopardy! game show
Built with Create React App
Backend architecture inspired by modern PHP frameworks
Contact
For questions or issues, please open an issue on GitHub.

Enjoy testing your trivia knowledge! 🎉

