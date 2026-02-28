function ResultCard({ result, correctAnswer, onNext, isLast }) {
    const isCorrect = result === 'correct';

    return (
        <div className="main-panel" style={{ textAlign: 'center' }}>
            <div className={`result-icon-wrap ${isCorrect ? 'correct' : 'incorrect'}`}>
                {isCorrect ? (
                    <svg width="36" height="36" fill="none" stroke="#00ff87" strokeWidth="2.5" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                ) : (
                    <svg width="36" height="36" fill="none" stroke="#ff3d6b" strokeWidth="2.5" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                )}
            </div>

            <div className={`result-label ${isCorrect ? 'correct' : 'incorrect'}`}>
                {isCorrect ? 'Correct' : 'Incorrect'}
            </div>

            <div className="correct-answer-box">
                <div className="correct-answer-label">Correct Answer</div>
                <div className="correct-answer-text">{correctAnswer}</div>
            </div>

            <button className="btn-primary" onClick={onNext}>
                {isLast ? 'View Results' : 'Next Question'}
            </button>
        </div>
    );
}

export default ResultCard;
