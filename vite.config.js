import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const isBuildCommand = process.argv.includes('build');

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: !isBuildCommand,
        }),
    ],
});
