/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/**/*.{js,jsx,ts,tsx}",
    ],
    theme: {
        extend: {
            fontFamily: {
                orbitron: ['Orbitron', 'sans-serif'],
            },
            colors: {
                cyber: {
                    bg:     '#050a14',
                    panel:  '#0a1628',
                    border: '#00e5ff',
                    accent: '#00e5ff',
                    green:  '#00ff87',
                    red:    '#ff3d6b',
                    muted:  '#1a2a44',
                    text:   '#c8eeff',
                },
            },
        },
    },
    plugins: [],
}
