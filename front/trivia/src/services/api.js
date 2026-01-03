const API_BASE = 'http://localhost:8000/api/clues';

export async function fetchRandomClue() {
    try {
        const response = await fetch(`${API_BASE}/random`);
        const data = await response.json();

        console.log('API Response:', data); // Debug log

        if (data.success) {
            console.log('Received clue data:', data.data.clue);
            return data.data.clue; // Updated path
        } else {
            throw new Error(data.error || 'Failed to fetch clue');
        }
    } catch (error) {
        console.error('Fetch error:', error); // Debug log
        throw new Error(error.message || 'Network error. Please try again.');
    }
}

export default {
    fetchRandomClue
};