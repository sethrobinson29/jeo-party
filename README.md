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
Frontend: React 18.2 Tailwind CSS 3.4
Backend: PHP 8.2.27
External API: Open Trivia Database - Free trivia questions API

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

Future Endpoints
GET  /clues/category/:category    - Get clues by category
GET  /clues/difficulty/:difficulty - Get clues by difficulty
POST /clues/validate               - Validate an answer
Extending the Backend
Adding a New Trivia Service
The application uses a service-oriented architecture. To add a new trivia API:

1. Create a new service class
   php

2. Update the controller

Security Features
The backend includes several security measures (or will, rather, since many of these haven't been implemented):

✅ Request validation (XSS, SQL injection, path traversal detection)

✅ HTTP method validation

✅ URI length limits

✅ Security headers (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection)

✅ Directory listing disabled

✅ Sensitive file access blocked

✅ Rate limiting hooks (ready for Redis/Memcached)

License
This project is open source and available under the MIT License.

Acknowledgments

Trivia questions provided by Open Trivia Database.Inspired by the classic Jeopardy! game show. Built with Create React App. Backend architecture inspired by modern PHP frameworks.
