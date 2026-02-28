function CategorySelect({ categories, onSelect, onBack }) {
    return (
        <div className="category-select">
            <h2 className="section-heading">Select Category</h2>
            <div className="category-grid">
                {categories.map(cat => (
                    <button
                        key={cat.id}
                        className="category-tile"
                        onClick={() => onSelect(cat)}
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
