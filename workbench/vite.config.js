import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'app/Filament/**',
                'vendor/eclipsephp/**/Livewire/**',
                'vendor/eclipsephp/**/Filament/**',
            ],
        }),
    ],
    server: {
        https: false,
        host: true,
        port: 3009,
        hmr: {host: 'localhost', protocol: 'ws'},
    },
});
