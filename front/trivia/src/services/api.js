const API_BASE = 'http://localhost:8000/api/clues';

export async function fetchRandomClue() {
    const response = await fetch(`${API_BASE}/random`);
    const data = await response.json();

    if (!data.success) {
        throw new Error(data.error || 'Failed to fetch clue');
    }

    return data.data.clue;
}

export default { fetchRandomClue };
