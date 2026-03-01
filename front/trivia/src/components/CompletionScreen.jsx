function CompletionScreen({
    score, total, mode, categoryName, difficultyName,
    onPlayAgain, onCategorySelect, onDifficultySelect, onHome,
}) {
    const playAgainLabel = {
        random:     'Play Again',
        category:   `5 More: ${categoryName}`,
        difficulty: `50 More: ${difficultyName ? difficultyName.charAt(0).toUpperCase() + difficultyName.slice(1) : ''}`,
        board:      'New Board',
    }[mode] ?? 'Play Again';

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
                    {playAgainLabel}
                </button>
                {mode === 'category' && (
                    <button className="btn-secondary" onClick={onCategorySelect}>
                        Choose Another Category
                    </button>
                )}
                {mode === 'difficulty' && (
                    <button className="btn-secondary" onClick={onDifficultySelect}>
                        Choose Difficulty
                    </button>
                )}
                <button className="btn-ghost" onClick={onHome}>Main Menu</button>
            </div>
        </div>
    );
}

export default CompletionScreen;
