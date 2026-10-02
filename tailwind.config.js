import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Local-Farm-Fresh design tokens.
 *
 * Contrast note: white text on brand-500 (#2E9B50) is 3.55:1, below WCAG AA
 * for normal text. Buttons and green text therefore use brand-600 (#23823F,
 * 4.84:1). brand-500 is for icons, accents, focus rings and large display type.
 */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#F3FAF4',   // very light green
                    100: '#E6F5E8',  // light mint
                    200: '#C9E9CF',
                    300: '#43B85F',  // fresh green (decorative only)
                    500: '#2E9B50',  // primary green (accents, icons)
                    600: '#23823F',  // primary action (AA with white text)
                    800: '#174C32',  // dark agricultural green
                    900: '#0F3D27',  // deep green
                },
                ink: 'rgb(var(--ink) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                line: 'rgb(var(--line) / <alpha-value>)',
                canvas: 'rgb(var(--canvas) / <alpha-value>)',
            },
            borderRadius: {
                card: '1.25rem',
            },
            boxShadow: {
                card: '0 1px 2px rgba(24, 53, 42, 0.04), 0 4px 16px rgba(24, 53, 42, 0.06)',
            },
        },
    },

    plugins: [forms],
};
