/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {}
  },
  daisyui: {
    themes: ['light', 'dark', 'cupcake']
  },
  plugins: [require('daisyui')]
}
