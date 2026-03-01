const POINT_VALUES = [200, 400, 600, 800, 1000];

function JeopardyBoard({ boardData, boardAnswers, onSelectCell, onHome }) {
    const cols = boardData.length;

    return (
        <div className="jeopardy-board">
            <p className="section-heading">Play A Round</p>
            <div
                className="board-grid"
                style={{ gridTemplateColumns: `repeat(${cols}, 1fr)` }}
            >
                {boardData.map((cat, catIdx) => (
                    <div key={catIdx} className="board-category-header">
                        {cat.category}
                    </div>
                ))}

                {POINT_VALUES.map((value, clueIdx) =>
                    boardData.map((_, catIdx) => {
                        const key = `${catIdx}-${clueIdx}`;
                        const answered = boardAnswers[key];
                        return (
                            <button
                                key={key}
                                className={`board-cell${answered ? ` board-cell--${answered}` : ''}`}
                                onClick={() => !answered && onSelectCell(catIdx, clueIdx)}
                                disabled={!!answered}
                            >
                                {!answered && `$${value}`}
                            </button>
                        );
                    })
                )}
            </div>

            <button className="btn-ghost" onClick={onHome}>Main Menu</button>
        </div>
    );
}

export default JeopardyBoard;
