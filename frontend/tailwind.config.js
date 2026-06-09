module.exports = {
    prefix: 'rsf-',
    safelist: [
        'rsf-grid',
        'rsf-grid-cols-1',
        'rsf-grid-cols-12',
        'rsf-gap-x-6',
        'rsf-gap-y-3',
        'rsf-gap-y-4',
        'rsf-items-start',
        'rsf-col-span-1',
        'rsf-min-w-0',
        {
            pattern: /^(sm:|md:|lg:|xl:)?rsf-col-span-(1[0-2]|[1-9])$/,
        },
        {
            pattern: /^(sm:|md:|lg:|xl:)?rsf-grid-cols-(1|12)$/,
        },
    ],
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
