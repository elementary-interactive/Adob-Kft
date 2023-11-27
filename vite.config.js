import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    build: {
        server: {
            https: true,
        },
    },
    plugins: [
        // inject({
        //     $: 'jquery',
        //     jQuery: 'jquery',
        // }),
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js', 'resources/js/splide.min.js', 'resources/js/site.js', 'resources/css/filament/admin2/theme.css'],
            refresh: false,
        }),

    ],
    server: {
        port: 8000
    },
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap')
        }
    }
});
