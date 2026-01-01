import React from 'react';

function StartScreen({ onStart }) {
    return (
        <div className="p-12 text-center">
            <p className="text-blue-300 text-lg mb-6">Ready to test your knowledge?</p>
            <button
                onClick={onStart}
                className="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold py-4 px-8 rounded-lg text-xl transition-colors shadow-lg"
            >
                Get Random Clue
            </button>
        </div>
    );
}

export default StartScreen;