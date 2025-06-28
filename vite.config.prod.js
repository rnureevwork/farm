import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import vuetify from 'vite-plugin-vuetify';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: false, // Отключаем HMR для продакшена
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vuetify({
            autoImport: true,
        }),
    ],
    build: {
        chunkSizeWarningLimit: 1600,
        outDir: 'public/build',
        assetsDir: '',
        manifest: true,
        sourcemap: false, // Отключаем source maps для продакшена
        minify: 'terser',
        rollupOptions: {
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia'],
                    vuetify: ['vuetify'],
                },
            },
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    define: {
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
    },
}); 