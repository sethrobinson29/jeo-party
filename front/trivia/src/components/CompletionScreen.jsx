function CompletionScreen({ score, total, mode, categoryName, onPlayAgain, onCategorySelect, onHome }) {
    return (
        <div className="completion-screen">
            <h2 className="completion-title">Session Complete</h2>
            <div className="score-display">
                <span className="score-value">{score}</span>
                <span className="score-divider">/</span>
                <span className="score-total">{total}</span>
            </div>
            <p className="score-label">Correct</p>
            <div className="completion-actions">
                <button className="btn-primary" onClick={onPlayAgain}>
                    {mode === 'category' ? `5 More: ${categoryName}` : 'Play Again'}
                </button>
                {mode === 'category' && (
                    <button className="btn-secondary" onClick={onCategorySelect}>
                        Choose Another Category
                    </button>
                )}
                <button className="btn-ghost" onClick={onHome}>Main Menu</button>
            </div>
        </div>
    );
}

export default CompletionScreen;
