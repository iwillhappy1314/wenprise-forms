module.exports = {
    content   : [
        '../src/**/*.php',
        '../templates/**/*.php'
    ],
    //darkMode: false, // or 'media' or 'class'
    theme   : {
        extend: {},
    },
    variants: {
        extend: {},
    },
    plugins : [
        require('@tailwindcss/typography'),
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/aspect-ratio')
    ],
};