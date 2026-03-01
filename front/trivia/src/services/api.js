const BASE = 'http://localhost:8000';

async function apiFetch(path) {
    const response = await fetch(`${BASE}${path}`);
    const data = await response.json();

    if (!data.success) {
        throw new Error(data.error || 'Request failed');
    }

    return data.data;
}

export async function fetchRandomClue() {
    const data = await apiFetch('/api/clues/random');
    return data.clue;
}

export async function fetchBatchClues(count = 61) {
    const data = await apiFetch(`/api/clues/batch?count=${count}`);
    return data.clues;
}

export async function fetchCategories() {
    const data = await apiFetch('/api/categories');
    return data.categories;
}

export async function fetchCluesByCategory(categoryId, count = 5) {
    const data = await apiFetch(`/api/clues/category/${categoryId}?count=${count}`);
    return data.clues;
}

export async function fetchCluesByDifficulty(difficulty, count = 50) {
    const data = await apiFetch(`/api/clues/difficulty/${difficulty}?count=${count}`);
    return data.clues;
}

export async function fetchBoardClues() {
    const data = await apiFetch('/api/clues/board');
    return data.board;
}
