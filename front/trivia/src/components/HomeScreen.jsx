function HomeScreen({ onRandomMode, onCategoryMode, onDifficultyMode, onBoardMode }) {
    return (
        <div className="home-screen">
            <p className="home-tagline">Select Your Challenge</p>
            <div className="mode-grid">
                <button className="mode-tile mode-tile--random" onClick={onRandomMode}>
                    <span className="mode-icon">∞</span>
                    <span className="mode-title">Random</span>
                    <span className="mode-desc">50 questions from all categories</span>
                </button>
                <button className="mode-tile mode-tile--category" onClick={onCategoryMode}>
                    <span className="mode-icon">◈</span>
                    <span className="mode-title">By Category</span>
                    <span className="mode-desc">5 questions from your chosen topic</span>
                </button>
                <button className="mode-tile mode-tile--difficulty" onClick={onDifficultyMode}>
                    <span className="mode-icon">⚡</span>
                    <span className="mode-title">By Difficulty</span>
                    <span className="mode-desc">50 questions at your skill level</span>
                </button>
                <button className="mode-tile mode-tile--board" onClick={onBoardMode}>
                    <span className="mode-icon">▦</span>
                    <span className="mode-title">Jeopardy Board</span>
                    <span className="mode-desc">6 categories, 5 questions each</span>
                </button>
            </div>
        </div>
    );
}

export default HomeScreen;
