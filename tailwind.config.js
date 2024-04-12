import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import("tailwindcss").Config} */

export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: "#0891b2",
                darker: "#374151",
                dark: "#4b5563",
                softDark: "#9ca3af",
                warning: "#fbbf24",
                danger: "#f87171",
                success: "#4ade80",
            },
        },
    },

    plugins: [forms],
};
