import { useState } from 'react';
import HomeScreen from './components/HomeScreen';
import LoadingScreen from './components/LoadingScreen';
import CategorySelect from './components/CategorySelect';
import ClueCard from './components/ClueCard';
import ResultCard from './components/ResultCard';
import CompletionScreen from './components/CompletionScreen';
import ErrorMessage from './components/ErrorMessage';
import { fetchBatchClues, fetchCategories, fetchCluesByCategory } from './services/api';

function App() {
    const [screen, setScreen] = useState('home');
    const [mode, setMode] = useState(null);

    const [clues, setClues] = useState([]);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [score, setScore] = useState(0);

    const [answer, setAnswer] = useState('');
    const [result, setResult] = useState(null);
    const [validationError, setValidationError] = useState('');

    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);

    const [error, setError] = useState(null);

    const validateInput = (value) => /^[a-zA-Z0-9\s'''\-&.,!?]*$/.test(value);

    const handleAnswerChange = (value) => {
        setAnswer(value);
        setValidationError(validateInput(value) ? '' : 'Answer contains unsupported characters');
    };

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
            setValidationError('');
        }
    };

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
            setValidationError('');
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
            setValidationError('');
            setScreen('playing');
        } catch (err) {
            setError(err.message || 'Failed to load questions');
            setScreen('category-select');
        }
    };

    const playAgain = () => {
        if (mode === 'random') startRandom();
        else if (mode === 'category' && selectedCategory) startCategoryGame(selectedCategory);
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
        setValidationError('');
    };

    const currentClue = clues[currentIndex];
    const isSubmitDisabled = !answer.trim() || validationError !== '' || result !== null;
    const isLastClue = currentIndex + 1 >= clues.length;

    return (
        <div className="app-root">
            <header className="app-header">
                <h1 className="app-title" onClick={goHome} style={{ cursor: 'pointer' }}>JEOPARDY!</h1>
                {screen === 'playing' && (
                    <div className="progress-indicator">
                        {currentIndex + 1} / {clues.length}
                    </div>
                )}
            </header>

            {error && <ErrorMessage message={error} onDismiss={() => setError(null)} />}

            {screen === 'home' && (
                <HomeScreen onRandomMode={startRandom} onCategoryMode={startCategoryMode} />
            )}

            {screen === 'loading' && <LoadingScreen />}

            {screen === 'category-select' && (
                <CategorySelect categories={categories} onSelect={startCategoryGame} onBack={goHome} />
            )}

            {screen === 'playing' && currentClue && result === null && (
                <ClueCard
                    clue={currentClue}
                    answer={answer}
                    validationError={validationError}
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

            {screen === 'complete' && (
                <CompletionScreen
                    score={score}
                    total={clues.length}
                    mode={mode}
                    categoryName={selectedCategory?.name}
                    onPlayAgain={playAgain}
                    onCategorySelect={() => setScreen('category-select')}
                    onHome={goHome}
                />
            )}
        </div>
    );
}

export default App;
