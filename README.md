Jeopardy Trivia Game
A full-stack trivia game application built with React and PHP, featuring Jeopardy-style questions from the Open Trivia Database.

Features
🎮 Interactive trivia gameplay with random questions
✅ Real-time input validation (letters and numbers only)
🎨 Beautiful Jeopardy-themed UI with animations
⌨️ Keyboard support (press Enter to submit)
🔄 Quick "Next Question" functionality
📱 Responsive design
Tech Stack
Frontend:

React 18.2
Tailwind CSS 3.4
Modern hooks-based architecture
Backend:

PHP 8.2
cURL for API requests
RESTful API wrapper
External API: Open Trivia Database - Free trivia questions API

Prerequisites
Node.js 14+ and npm
PHP 8.2 with cURL extension enabled
Modern web browser
Installation
1. Clone the repository
bash
git clone <your-repo-url>
cd trivia
2. Install frontend dependencies
bash
npm install
3. Configure the backend
Make sure api.php is in your project root and the PHP server can access it.

Running the Application
You need to run two servers simultaneously:

Terminal 1: PHP Backend (Port 8000)
bash
php -S localhost:8000
This starts the PHP API wrapper that communicates with Open Trivia DB.

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
API Endpoints
PHP Backend (api.php)
GET http://localhost:8000/api.php?endpoint=random-clue

Returns a normalized trivia question:

json
{
  "success": true,
  "clue": {
    "category": "Science",
    "clue": "This is the chemical symbol for gold",
    "response": "Au",
    "difficulty": "easy",
    "type": "multiple"
  }
}
Configuration
Change API Base URL
Edit src/services/api.js:

javascript
const API_BASE = 'http://localhost:8000/api.php';
Modify Validation Rules
Edit the validateInput function in src/App.jsx:

javascript
const validateInput = (value) => {
  const regex = /^[a-zA-Z0-9\s]*$/;
  return regex.test(value);
};
Development
Available Scripts
npm start - Start development server
npm run build - Build for production
npm test - Run tests
npm run eject - Eject from Create React App (one-way operation)
Component Development
All components are functional components using React Hooks. To add a new component:

Create ComponentName.jsx in src/components/
Import and use in App.jsx
Production Deployment
Build the React App
bash
npm run build
This creates optimized production files in the build/ folder.

Deploy Backend
Upload api.php to your PHP hosting
Ensure cURL extension is enabled
Update CORS headers in api.php if needed
Deploy Frontend
Upload the contents of the build/ folder to your web host, or use services like:

Vercel
Netlify
GitHub Pages
Update the API base URL in production to point to your hosted PHP backend.

Troubleshooting
Port Already in Use
If port 3000 or 8000 is in use:

bash
# For React (use different port)
PORT=3001 npm start

# For PHP (use different port)
php -S localhost:8001
Remember to update the API base URL if you change the PHP port.

SSL Certificate Errors
If you encounter SSL errors with the Open Trivia DB API, check api.php line 24:

php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
Note: Only disable SSL verification for local development.

Cache Issues
Clear browser cache or use incognito mode if changes aren't appearing.

bash
# Clear React cache
rm -rf node_modules/.cache
npm start
PhpStorm File Hiding
If using PhpStorm, check that it's not hiding duplicate files (like App.js inside App.jsx). Delete any duplicate files.

Future Enhancements
 Score tracking
 Difficulty selection
 Category filtering
 Timer for answers
 Leaderboard
 Multiple choice options display
 Answer history
Contributing
Fork the repository
Create a feature branch (git checkout -b feature/amazing-feature)
Commit your changes (git commit -m 'Add amazing feature')
Push to the branch (git push origin feature/amazing-feature)
Open a Pull Request
License
This project is open source and available under the MIT License.

Acknowledgments
Trivia questions provided by Open Trivia Database
Inspired by the classic Jeopardy! game show
Built with Create React App
Contact
For questions or issues, please open an issue on GitHub.

Enjoy testing your trivia knowledge! 🎉

