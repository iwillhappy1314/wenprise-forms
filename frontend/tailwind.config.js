module.exports = {
    prefix: 'rsf-',
    content   : [
        '../src/**/*.php',
        '../vendor/**/*.php',
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
        require('@tailwindcss/aspect-ratio')
    ],
};