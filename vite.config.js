import {defineConfig} from "vite";

/** @type {import('vite').UserConfig} */
export default defineConfig({
    build: {
        assetsDir: '',
        manifest: true,
        rollupOptions: {
            input: [
                'resources/js/powergrid.js',
                'resources/css/tailwind.css',
            ],
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
            },
        },
    },
});
