function ClueCard({ clue, answer, isSubmitDisabled, onAnswerChange, onSubmit }) {
    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !isSubmitDisabled) {
            onSubmit();
        }
    };

    return (
        <div className="main-panel">
            <div style={{ textAlign: 'center', marginBottom: '0.5rem' }}>
                <div className="category-badge">Category</div>
                <div className="category-name">{clue.category}</div>
            </div>

            <div className="clue-box">{clue.clue}</div>

            <div>
                <label className="answer-label">What is...</label>
                <input
                    type="text"
                    value={answer}
                    onChange={(e) => onAnswerChange(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder="Type your answer here"
                    className="answer-input"
                    autoFocus
                />
            </div>

            <button className="btn-primary" onClick={onSubmit} disabled={isSubmitDisabled}>
                Submit Answer
            </button>
        </div>
    );
}

export default ClueCard;
