import React from 'react';

function ClueCard({
                      clue,
                      answer,
                      validationError,
                      isSubmitDisabled,
                      onAnswerChange,
                      onSubmit
                  }) {
    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !isSubmitDisabled) {
            onSubmit();
        }
    };

    const hasValidationError = validationError !== '';

    return (
        <div className="p-8">
            {/* Category */}
            <div className="text-center mb-6">
                <div className="inline-block bg-blue-800 px-6 py-2 rounded-lg border-2 border-yellow-400">
                    <p className="text-yellow-400 font-bold text-sm uppercase tracking-wider">
                        Category
                    </p>
                    <p className="text-white text-xl font-semibold">{clue.category}</p>
                </div>
            </div>

            {/* Clue */}
            <div className="bg-blue-900 p-6 rounded-lg mb-6 border-2 border-blue-700">
                <p className="text-white text-2xl text-center leading-relaxed">
                    {clue.clue}
                </p>
            </div>

            {/* Answer Input */}
            <div className="mb-4">
                <label className="block text-blue-200 mb-2 font-semibold">
                    What is...
                </label>
                <input
                    type="text"
                    value={answer}
                    onChange={(e) => onAnswerChange(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder="Type your answer here"
                    className={`w-full px-4 py-3 rounded-lg text-lg focus:outline-none focus:ring-2 ${
                        hasValidationError
                            ? 'bg-red-100 border-2 border-red-500 focus:ring-red-500'
                            : 'bg-white text-blue-900 focus:ring-yellow-400'
                    }`}
                    autoFocus
                />
                {hasValidationError && (
                    <p className="text-red-400 text-sm mt-2 flex items-center">
                        <span className="mr-1">⚠️</span>
                        {validationError}
                    </p>
                )}
            </div>

            {/* Submit Button */}
            <button
                onClick={onSubmit}
                disabled={isSubmitDisabled}
                className={`w-full font-bold py-3 px-6 rounded-lg text-xl transition-all ${
                    isSubmitDisabled
                        ? 'bg-gray-600 text-gray-400 cursor-not-allowed'
                        : 'bg-yellow-400 hover:bg-yellow-500 text-blue-900 shadow-lg hover:shadow-xl'
                }`}
            >
                Submit Answer
            </button>
        </div>
    );
}

export default ClueCard;