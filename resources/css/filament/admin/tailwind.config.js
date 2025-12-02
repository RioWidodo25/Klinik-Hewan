import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    safelist: [
        // Service icon colors - safelist untuk badge colors di table
        {
            pattern: /bg-(amber|blue|green|red|purple|pink|indigo|teal|orange|cyan|emerald|lime|rose|sky|violet)-600/,
        },
    ],
}
