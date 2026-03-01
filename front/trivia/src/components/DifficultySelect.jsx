const DIFFICULTIES = [
    { id: 'easy',   label: 'Easy',   icon: '◎', desc: 'Beginner-friendly questions' },
    { id: 'medium', label: 'Medium', icon: '◈', desc: 'A proper challenge'           },
    { id: 'hard',   label: 'Hard',   icon: '◉', desc: 'For the brave only'           },
];

function DifficultySelect({ onSelect, onBack }) {
    return (
        <div className="difficulty-select">
            <p className="section-heading">Choose Difficulty</p>
            <div className="difficulty-grid">
                {DIFFICULTIES.map(d => (
                    <button
                        key={d.id}
                        className={`mode-tile difficulty-tile--${d.id}`}
                        onClick={() => onSelect(d.id)}
                    >
                        <span className="mode-icon">{d.icon}</span>
                        <span className="mode-title">{d.label}</span>
                        <span className="mode-desc">{d.desc}</span>
                    </button>
                ))}
            </div>
            <button className="btn-ghost" style={{ marginTop: '1.5rem' }} onClick={onBack}>
                Back
            </button>
        </div>
    );
}

export default DifficultySelect;
