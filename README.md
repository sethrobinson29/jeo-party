# Jeo Party

A full-stack Jeopardy-style trivia game. Questions are sourced from the [Open Trivia Database](https://opentdb.com).

## Tech Stack

- **Frontend:** React 18.2, Tailwind CSS 3.4
- **Backend:** PHP 8.2, custom MVC framework (no Composer)

## Prerequisites

- PHP 8.2+ with the `curl` extension enabled
- Node.js 18+ and npm

## Setup

```bash
git clone <repo-url>
cd jeo-party

# Install frontend dependencies
cd front/trivia && npm install
```

**Windows only:** PHP's curl does not ship with a CA bundle on Windows. Download the Mozilla CA bundle before starting the backend:

```bash
curl -o backend/cacert.pem https://curl.se/ca/cacert.pem
```

## Running

Two servers must run simultaneously.

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

## How to Play

- **Random** — answers 50 questions drawn at random, then shows a completion screen
- **By Category** — choose a category, answer 5 questions, then opt to continue in the same category, pick another, or return home

Answers are checked case-insensitively. The title is a home link at any point.

## API

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/clues/random` | Single random clue |
| GET | `/api/clues/batch?count=N` | Batch of clues (max 50) |
| GET | `/api/clues/category/:id?count=N` | Clues for a category (default 5) |
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
                            # CompletionScreen, LoadingScreen, ErrorMessage
  services/api.js           # Backend communication
```
