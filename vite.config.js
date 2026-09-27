import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',
        allowedHosts: [],
        hmr: {
            host: '127.0.0.1',
        },
    },
});
