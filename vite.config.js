import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/main.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    server: {
        host: '192.168.1.146',
        port: 3000,
        hmr: {
            host: '192.168.1.146',
        }
    }
});
