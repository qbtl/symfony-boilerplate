const colors = require("tailwindcss/colors");
module.exports = {
    content: ["./templates/**/*.html.twig"],
    theme: {
        extend: {
            screens: {
                mobile: "320px",
                xs: "475px",
                tablet: "860px",
                desktop: "1390px",
            },
            colors: {
                sky: colors.sky,
                gray: {
                    850: "hsl(216.52, 32.39%, 13.92%)",
                },
                accent: {
                    red: "#FFE0E0",
                    green: "#D2F0CD",
                    yellow: "#F9EDC7",
                },
            },
            typography: () => ({
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                    }
                }
            }),
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/aspect-ratio"),
        require("@tailwindcss/typography"),
    ],
};