# Jeo Party

A full-stack Jeopardy-style trivia game. Questions are sourced from the [Open Trivia Database](https://opentdb.com) (CC BY-SA 4.0).

## Tech Stack

- **Frontend:** React 18.2, Tailwind CSS 3.4
- **Backend:** PHP 8.2, custom MVC framework (no Composer)

## Prerequisites

- PHP 8.2+ with the `curl` extension enabled
- Node.js 18+ and npm

## Local Development

```bash
git clone <repo-url>
cd jeo-party
cd front/trivia && npm install
```

**Windows only:** PHP's curl does not ship with a CA bundle on Windows. Download it before starting the backend:

```bash
curl -o backend/cacert.pem https://curl.se/ca/cacert.pem
```

**Frontend API URL:** Create `front/trivia/.env.local` to point the frontend at the local backend:

```
REACT_APP_API_URL=http://localhost:8000
```

Run both servers simultaneously:

**Backend** (port 8000):
```bash
cd backend
php -S localhost:8000 index.php
```

**Frontend** (port 3000):
```bash
cd front/trivia
npm start
```

Open [http://localhost:3000](http://localhost:3000).

## Production Deployment

Requires nginx and PHP-FPM. PHP 8.2 packages are available via the `ondrej/php` PPA on Ubuntu:

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2-fpm php8.2-curl
```

Build the frontend:

```bash
cd front/trivia
npm install
npm run build
```

Create the logs directory with correct ownership:

```bash
mkdir -p backend/logs
sudo chown www-data:www-data backend/logs
```

**nginx config** (`/etc/nginx/sites-available/jeo-party`):

```nginx
limit_req_zone $binary_remote_addr zone=api:10m rate=20r/m;

server {
    listen 80;
    server_name yourdomain.com;

    server_tokens off;

    add_header X-Frame-Options       "SAMEORIGIN"           always;
    add_header X-Content-Type-Options "nosniff"             always;
    add_header Referrer-Policy       "strict-origin-when-cross-origin" always;

    root /var/www/jeo-party/front/trivia/build;
    index index.html;

    location / {
        try_files $uri /index.html;
    }

    location /api/ {
        limit_req zone=api burst=10 nodelay;

        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME /var/www/jeo-party/backend/index.php;
        fastcgi_param REQUEST_URI $request_uri;
        include fastcgi_params;
    }
}
```

**HTTPS** (requires a domain with DNS pointing to the server):

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

**Future deploys:**

```bash
cd /var/www/jeo-party
git pull
cd front/trivia && npm install && npm run build
```

## How to Play

- **Random** — 50 questions drawn at random from all categories
- **By Category** — choose a category, answer 5 questions
- **By Difficulty** — 50 questions filtered by easy, medium, or hard
- **Jeopardy Board** — 6 categories × 5 questions, select clues by point value

Answers are checked case-insensitively. Non-alphanumeric characters (except common punctuation) are stripped from the answer input. The title is a home link at any point.

## API

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/clues/random` | Single random clue |
| GET | `/api/clues/batch?count=N` | Batch of N clues (max 50) |
| GET | `/api/clues/category/:id?count=N` | N clues for a category (default 5) |
| GET | `/api/clues/difficulty/:level` | 50 clues filtered by easy/medium/hard |
| GET | `/api/clues/board` | 6 categories × 5 clues for the board |
| GET | `/api/categories` | List of available categories |

All responses follow `{ success: bool, data?: any, error?: string }`.

## Project Structure

```
backend/
  config/routes.php         # Route definitions
  src/
    Controllers/            # ClueController, CategoryController
    Core/                   # Application, Router, Request, Response
    Logging/                # PSR-3 FileLogger, static Logger facade
    Psr/Log/                # Psr\Log interface stubs (no Composer)
    Services/               # OpenTriviaService, LocalJeopardyService
  logs/                     # Runtime logs (gitignored)

front/trivia/src/
  App.jsx                   # All game state, screen-based state machine
  components/               # HomeScreen, ClueCard, ResultCard, CategorySelect,
                            # DifficultySelect, JeopardyBoard, CompletionScreen,
                            # LoadingScreen, ErrorMessage
  services/api.js           # Backend communication
```
