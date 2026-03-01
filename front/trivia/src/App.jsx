import { useState } from 'react';
import HomeScreen from './components/HomeScreen';
import LoadingScreen from './components/LoadingScreen';
import CategorySelect from './components/CategorySelect';
import DifficultySelect from './components/DifficultySelect';
import JeopardyBoard from './components/JeopardyBoard';
import ClueCard from './components/ClueCard';
import ResultCard from './components/ResultCard';
import CompletionScreen from './components/CompletionScreen';
import ErrorMessage from './components/ErrorMessage';
import {
    fetchBatchClues,
    fetchCategories,
    fetchCluesByCategory,
    fetchCluesByDifficulty,
    fetchBoardClues,
} from './services/api';

function App() {
    const [screen, setScreen] = useState('home');
    const [mode, setMode] = useState(null);

    // Linear play state (random / category / difficulty)
    const [clues, setClues] = useState([]);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [score, setScore] = useState(0);

    const [answer, setAnswer] = useState('');
    const [result, setResult] = useState(null);

    // Category mode
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);

    // Difficulty mode
    const [selectedDifficulty, setSelectedDifficulty] = useState(null);

    // Board mode
    const [boardData, setBoardData] = useState([]);
    const [boardAnswers, setBoardAnswers] = useState({});
    const [selectedBoardCell, setSelectedBoardCell] = useState(null);

    const [error, setError] = useState(null);

    const handleAnswerChange = (value) => {
        setAnswer(value.replace(/[^a-zA-Z0-9\s'''\-&.,!?]/g, ''));
    };

    // ─── Linear play ──────────────────────────────────────────

    const handleSubmit = () => {
        const current = clues[currentIndex];
        if (!current || !answer.trim()) return;

        const isCorrect = answer.trim().toLowerCase() === current.response.toLowerCase();
        setResult(isCorrect ? 'correct' : 'incorrect');
        if (isCorrect) setScore(s => s + 1);
    };

    const handleNext = () => {
        if (currentIndex + 1 >= clues.length) {
            setScreen('complete');
        } else {
            setCurrentIndex(i => i + 1);
            setAnswer('');
            setResult(null);
        }
    };

    // ─── Board play ───────────────────────────────────────────

    const selectBoardCell = (catIdx, clueIdx) => {
        setSelectedBoardCell({ catIdx, clueIdx });
        setAnswer('');
        setResult(null);
        setValidationError('');
        setScreen('board-clue');
    };

    const handleBoardSubmit = () => {
        if (!selectedBoardCell) return;
        const { catIdx, clueIdx } = selectedBoardCell;
        const clue = boardData[catIdx]?.clues[clueIdx];
        if (!clue || !answer.trim()) return;

        const isCorrect = answer.trim().toLowerCase() === clue.response.toLowerCase();
        const key = `${catIdx}-${clueIdx}`;
        setResult(isCorrect ? 'correct' : 'incorrect');
        if (isCorrect) setScore(s => s + 1);
        setBoardAnswers(prev => ({ ...prev, [key]: isCorrect ? 'correct' : 'incorrect' }));
    };

    const handleBoardNext = () => {
        const totalCells = boardData.reduce((sum, cat) => sum + cat.clues.length, 0);
        if (Object.keys(boardAnswers).length >= totalCells) {
            setScreen('complete');
        } else {
            setScreen('board-playing');
        }
        setAnswer('');
        setResult(null);
        setValidationError('');
        setSelectedBoardCell(null);
    };

    // ─── Mode starters ────────────────────────────────────────

    const startRandom = async () => {
        setMode('random');
        setScreen('loading');
        setError(null);
        try {
            const fetched = await fetchBatchClues(50);
            setClues(fetched);
            setCurrentIndex(0);
            setScore(0);
            setAnswer('');
            setResult(null);
            setScreen('playing');
        } catch (err) {
            setError(err.message || 'Failed to load questions');
            setScreen('home');
        }
    };

    const startCategoryMode = async () => {
        setMode('category');
        setScreen('loading');
        setError(null);
        try {
            const cats = await fetchCategories();
            setCategories(cats);
            setScreen('category-select');
        } catch (err) {
            setError(err.message || 'Failed to load categories');
            setScreen('home');
        }
    };

    const startCategoryGame = async (category) => {
        setSelectedCategory(category);
        setScreen('loading');
        setError(null);
        try {
            const fetched = await fetchCluesByCategory(category.id, 5);
            setClues(fetched);
            setCurrentIndex(0);
            setScore(0);
            setAnswer('');
            setResult(null);
            setScreen('playing');
        } catch (err) {
            setError(err.message || 'Failed to load questions');
            setScreen('category-select');
        }
    };

    const startDifficultyMode = () => {
        setMode('difficulty');
        setScreen('difficulty-select');
        setError(null);
    };

    const startDifficultyGame = async (difficulty) => {
        setSelectedDifficulty(difficulty);
        setScreen('loading');
        setError(null);
        try {
            const fetched = await fetchCluesByDifficulty(difficulty, 50);
            setClues(fetched);
            setCurrentIndex(0);
            setScore(0);
            setAnswer('');
            setResult(null);
            setScreen('playing');
        } catch (err) {
            setError(err.message || 'Failed to load questions');
            setScreen('difficulty-select');
        }
    };

    const startBoardMode = async () => {
        setMode('board');
        setScreen('loading');
        setError(null);
        try {
            const board = await fetchBoardClues();
            setBoardData(board);
            setBoardAnswers({});
            setScore(0);
            setScreen('board-playing');
        } catch (err) {
            setError(err.message || 'Failed to build board');
            setScreen('home');
        }
    };

    const playAgain = () => {
        if (mode === 'random') startRandom();
        else if (mode === 'category' && selectedCategory) startCategoryGame(selectedCategory);
        else if (mode === 'difficulty' && selectedDifficulty) startDifficultyGame(selectedDifficulty);
        else if (mode === 'board') startBoardMode();
    };

    const goHome = () => {
        setScreen('home');
        setMode(null);
        setClues([]);
        setCurrentIndex(0);
        setScore(0);
        setAnswer('');
        setResult(null);
        setError(null);
        setSelectedCategory(null);
        setSelectedDifficulty(null);
        setBoardData([]);
        setBoardAnswers({});
        setSelectedBoardCell(null);
        setValidationError('');
    };

    // ─── Derived values ───────────────────────────────────────

    const currentClue = clues[currentIndex];
    const boardClue = selectedBoardCell
        ? boardData[selectedBoardCell.catIdx]?.clues[selectedBoardCell.clueIdx]
        : null;
    const isSubmitDisabled = !answer.trim() || result !== null;
    const isLastClue = currentIndex + 1 >= clues.length;
    const boardTotal = boardData.reduce((sum, cat) => sum + cat.clues.length, 0);

    return (
        <div className="app-root">
            <header className="app-header">
                <h1 className="app-title" onClick={goHome} style={{ cursor: 'pointer' }}>JEO-PARTY!</h1>
                {screen === 'playing' && (
                    <div className="progress-indicator">
                        {currentIndex + 1} / {clues.length}
                    </div>
                )}
                {screen === 'board-clue' && (
                    <div className="progress-indicator">
                        {Object.keys(boardAnswers).length} / {boardTotal}
                    </div>
                )}
            </header>

            {error && <ErrorMessage message={error} onDismiss={() => setError(null)} />}

            {screen === 'home' && (
                <HomeScreen
                    onRandomMode={startRandom}
                    onCategoryMode={startCategoryMode}
                    onDifficultyMode={startDifficultyMode}
                    onBoardMode={startBoardMode}
                />
            )}

            {screen === 'loading' && <LoadingScreen />}

            {screen === 'category-select' && (
                <CategorySelect categories={categories} onSelect={startCategoryGame} onBack={goHome} />
            )}

            {screen === 'difficulty-select' && (
                <DifficultySelect onSelect={startDifficultyGame} onBack={goHome} />
            )}

            {screen === 'board-playing' && (
                <JeopardyBoard
                    boardData={boardData}
                    boardAnswers={boardAnswers}
                    onSelectCell={selectBoardCell}
                    onHome={goHome}
                />
            )}

            {screen === 'playing' && currentClue && result === null && (
                <ClueCard
                    clue={currentClue}
                    answer={answer}
                    isSubmitDisabled={isSubmitDisabled}
                    onAnswerChange={handleAnswerChange}
                    onSubmit={handleSubmit}
                />
            )}

            {screen === 'playing' && currentClue && result !== null && (
                <ResultCard
                    result={result}
                    correctAnswer={currentClue.response}
                    onNext={handleNext}
                    isLast={isLastClue}
                />
            )}

            {screen === 'board-clue' && boardClue && result === null && (
                <ClueCard
                    clue={boardClue}
                    answer={answer}
                    isSubmitDisabled={isSubmitDisabled}
                    onAnswerChange={handleAnswerChange}
                    onSubmit={handleBoardSubmit}
                />
            )}

            {screen === 'board-clue' && boardClue && result !== null && (
                <ResultCard
                    result={result}
                    correctAnswer={boardClue.response}
                    onNext={handleBoardNext}
                    isLast={false}
                />
            )}

            {screen === 'complete' && (
                <CompletionScreen
                    score={score}
                    total={mode === 'board' ? boardTotal : clues.length}
                    mode={mode}
                    categoryName={selectedCategory?.name}
                    difficultyName={selectedDifficulty}
                    onPlayAgain={playAgain}
                    onCategorySelect={() => setScreen('category-select')}
                    onDifficultySelect={() => setScreen('difficulty-select')}
                    onHome={goHome}
                />
            )}

            <footer className="app-footer">
                Questions provided by{' '}
                <a href="https://opentdb.com" target="_blank" rel="noopener noreferrer">
                    Open Trivia Database
                </a>{' '}
                (CC BY-SA 4.0)
            </footer>
        </div>
    );
}

export default App;
