import React, { useState } from 'react';
import ClueCard from './components/ClueCard';
import ResultCard from './components/ResultCard';
import StartScreen from './components/StartScreen';
import ErrorMessage from './components/ErrorMessage';
import { fetchRandomClue } from './services/api';

function App() {
    const [clue, setClue] = useState(null);
    const [answer, setAnswer] = useState('');
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [validationError, setValidationError] = useState('');

    const getRandomClue = async () => {
        setLoading(true);
        setError(null);
        setResult(null);
        setAnswer('');
        setValidationError('');

        try {
            const clueData = await fetchRandomClue();
            setClue(clueData);
        } catch (err) {
            setError(err.message || 'An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const validateInput = (value) => {
        const regex = /^[a-zA-Z0-9\s]*$/;
        return regex.test(value);
    };

    const handleAnswerChange = (value) => {
        setAnswer(value);

        if (!validateInput(value)) {
            setValidationError('Only letters and numbers are allowed');
        } else {
            setValidationError('');
        }
    };

    const handleSubmit = () => {
        if (!clue || !answer.trim()) return;

        const userAnswer = answer.trim().toLowerCase();
        const correctAnswer = clue.response.toLowerCase();

        if (userAnswer === correctAnswer) {
            setResult('correct');
        } else {
            setResult('incorrect');
        }
    };

    const resetGame = () => {
        setClue(null);
        setAnswer('');
        setResult(null);
        setError(null);
        setValidationError('');
    };

    const nextQuestion = async () => {
        await getRandomClue();
    };

    const hasValidationError = validationError !== '';
    const isSubmitDisabled = !answer.trim() || hasValidationError || !clue || result !== null;

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 flex items-center justify-center p-4">
            <div className="max-w-2xl w-full">
                {/* Header */}
                <div className="text-center mb-8">
                    <h1 className="text-5xl font-bold text-yellow-400 mb-2" style={{ fontFamily: 'serif' }}>
                        JEOPARDY!
                    </h1>
                    <p className="text-blue-200 text-sm">Test your trivia knowledge</p>
                </div>

                {/* Main Card */}
                <div className="bg-blue-950 rounded-lg shadow-2xl border-4 border-yellow-400 overflow-hidden">
                    {error && <ErrorMessage message={error} />}

                    {!clue && !loading && !error && (
                        <StartScreen onStart={getRandomClue} />
                    )}

                    {loading && (
                        <div className="p-12 text-center">
                            <div className="inline-block animate-spin rounded-full h-12 w-12 border-4 border-yellow-400 border-t-transparent mb-4"></div>
                            <p className="text-blue-300 text-lg">Loading clue...</p>
                        </div>
                    )}

                    {clue && !result && (
                        <ClueCard
                            clue={clue}
                            answer={answer}
                            validationError={validationError}
                            isSubmitDisabled={isSubmitDisabled}
                            onAnswerChange={handleAnswerChange}
                            onSubmit={handleSubmit}
                        />
                    )}

                    {result && clue && (
                        <ResultCard
                            result={result}
                            correctAnswer={clue.response}
                            onNextQuestion={nextQuestion}
                            onReset={resetGame}
                        />
                    )}
                </div>

                {/* Footer */}
                <div className="text-center mt-6">
                    <p className="text-blue-300 text-sm">
                        Powered by Open Trivia Database
                    </p>
                </div>
            </div>
        </div>
    );
}

export default App;