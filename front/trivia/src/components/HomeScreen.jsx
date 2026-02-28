function HomeScreen({ onRandomMode, onCategoryMode }) {
    return (
        <div className="home-screen">
            <p className="home-tagline">Select Your Challenge</p>
            <div className="mode-grid">
                <button className="mode-tile" onClick={onRandomMode}>
                    <span className="mode-icon">∞</span>
                    <span className="mode-title">Random</span>
                    <span className="mode-desc">50 questions from all categories</span>
                </button>
                <button className="mode-tile" onClick={onCategoryMode}>
                    <span className="mode-icon">◈</span>
                    <span className="mode-title">By Category</span>
                    <span className="mode-desc">5 questions from your chosen topic</span>
                </button>
            </div>
        </div>
    );
}

export default HomeScreen;
