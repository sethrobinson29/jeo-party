import { useState } from 'react';

function CategorySelect({ categories, onSelect, onBack }) {
    const [count, setCount] = useState(5);

    const handleCountChange = (e) => {
        const val = Math.min(50, Math.max(1, parseInt(e.target.value, 10) || 1));
        setCount(val);
    };

    return (
        <div className="category-select">
            <h2 className="section-heading">Select Category</h2>
            <div className="count-selector">
                <label className="count-label" htmlFor="question-count">Questions per category:</label>
                <input
                    id="question-count"
                    type="number"
                    className="count-input"
                    value={count}
                    min={1}
                    max={50}
                    onChange={handleCountChange}
                />
            </div>
            <div className="category-grid">
                {categories.map(cat => (
                    <button
                        key={cat.id}
                        className="category-tile"
                        onClick={() => onSelect(cat, count)}
                    >
                        {cat.name}
                    </button>
                ))}
            </div>
            <button className="btn-ghost" onClick={onBack}>← Back to Main Menu</button>
        </div>
    );
}

export default CategorySelect;
