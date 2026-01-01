import React from 'react';

function ResultCard({ result, correctAnswer, onNextQuestion, onReset }) {
    const isCorrect = result === 'correct';

    return (
        <div className="p-8">
            <div className="text-center">
                <div className="mb-4">
                    <div className={`inline-block rounded-full p-6 mb-4 ${
                        isCorrect ? 'bg-green-500 animate-bounce' : 'bg-red-500'
                    }`}>
                        {isCorrect ? (
                            <svg className="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M5 13l4 4L19 7" />
                            </svg>
                        ) : (
                            <svg className="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        )}
                    </div>
                    <h2 className={`text-4xl font-bold mb-2 ${
                        isCorrect ? 'text-green-400' : 'text-red-400'
                    }`}>
                        {isCorrect ? 'Correct!' : 'Not quite!'}
                    </h2>
                    <p className="text-blue-200 text-lg mb-4">
                        {isCorrect ? "That's right! 🎉" : 'Better luck next time'}
                    </p>
                </div>

                <div className={`bg-blue-900 p-4 rounded-lg mb-6 border-2 ${
                    isCorrect ? 'border-green-500' : 'border-red-500'
                }`}>
                    <p className={`text-sm uppercase mb-1 ${
                        isCorrect ? 'text-green-300' : 'text-red-300'
                    }`}>
                        The {isCorrect ? 'answer was' : 'correct answer was'}
                    </p>
                    <p className="text-white text-2xl font-semibold">{correctAnswer}</p>
                </div>
            </div>

            <button
                onClick={onNextQuestion}
                className="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold py-3 px-6 rounded-lg text-xl transition-colors shadow-lg mb-3"
            >
                Next Question
            </button>
            <button
                onClick={onReset}
                className="w-full bg-blue-700 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg text-lg transition-colors"
            >
                Back to Start
            </button>
        </div>
    );
}

export default ResultCard;