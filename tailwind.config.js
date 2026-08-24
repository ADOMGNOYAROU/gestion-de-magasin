import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Le reset de base ("preflight") est désactivé car la majorité des pages
    // utilisent Bootstrap 5 (chargé en CDN dans layouts/app.blade.php) —
    // sans ça, Tailwind écraserait les styles de base (headings, listes, etc.)
    // sur toutes les pages non-Tailwind. Le plugin forms reste en mode global
    // (par défaut) car les pages Breeze (login/register) en dépendent pour
    // le rendu normalisé des champs de formulaire.
    corePlugins: {
        preflight: false,
        // Tailwind's `.collapse { visibility: collapse }` (table utility)
        // collides with Bootstrap's `.collapse` component (used for the
        // sidebar accordion, dropdowns, etc.) since both frameworks share
        // the exact same class name for unrelated purposes.
        visibility: false,
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
