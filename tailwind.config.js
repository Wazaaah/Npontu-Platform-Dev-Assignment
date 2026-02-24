/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
      },
      colors: {
        sidebar: {
          bg:     '#0f172a',
          hover:  '#1e293b',
          active: '#1e293b',
          border: '#1e293b',
          text:   '#94a3b8',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
