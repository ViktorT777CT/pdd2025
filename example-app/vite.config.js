import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/css/desktop/index.css',
                'resources/css/desktop/end-test.css',
                'resources/css/desktop/test-a.css',
                'resources/css/desktop/test-page.css',
                'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
