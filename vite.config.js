import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import {AntDesignVueResolver} from "unplugin-vue-components/resolvers";
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,  // ou tout autre port que vous utilisez
    //     strictPort: true,
    //     hmr: {
    //         // host: '192.168.1.163',  // Remplacez par l'adresse IP de votre machine
    //         host: '192.168.1.113',  // Remplacez par l'adresse IP de votre machine
    //         port: 5173,
    //     }
    // },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        Components({
            resolvers: [
                AntDesignVueResolver({
                    importStyle: false, // css in js
                }),
            ],
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            outDir: 'public',
            base: '/build/',
            includeAssets: ['favicon.ico', 'icons/*.png'],
            manifest: {
                name: 'TransMada TMS',
                short_name: 'TransMada',
                description: 'Transport Management System - DNA',
                theme_color: '#112e79',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/',
                scope: '/',
                icons: [
                    { src: '/icons/icon-72x72.png',   sizes: '72x72',   type: 'image/png' },
                    { src: '/icons/icon-96x96.png',   sizes: '96x96',   type: 'image/png' },
                    { src: '/icons/icon-128x128.png', sizes: '128x128', type: 'image/png' },
                    { src: '/icons/icon-144x144.png', sizes: '144x144', type: 'image/png' },
                    { src: '/icons/icon-152x152.png', sizes: '152x152', type: 'image/png' },
                    { src: '/icons/icon-192x192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-384x384.png', sizes: '384x384', type: 'image/png' },
                    { src: '/icons/icon-512x512.png', sizes: '512x512', type: 'image/png', purpose: 'any maskable' },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                navigateFallback: null,
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/fonts\.(googleapis|gstatic)\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 * 365 },
                        },
                    },
                ],
            },
        }),
    ],
});
