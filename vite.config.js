import {defineConfig} from 'vite'
import checker from 'vite-plugin-checker'
import vue from '@vitejs/plugin-vue'
import symfonyPlugin from 'vite-plugin-symfony'

export default defineConfig({
    base: '/build/',
    build: {
        assetsDir: '',
        emptyOutDir: true,
        manifest: true,
        outDir: './public/build/',
        rollupOptions: {
            input: {index: './assets/index.js'}
        }
    },
    plugins: [
        symfonyPlugin(),
        vue(),
        checker({eslint: {lintCommand: 'eslint -c .eslintrc.js ./assets/**/*.{js,vue}'}})
    ],
    root: './',
    server: {
        force: true,
        fs: {
            allow: ['..'],
            strict: false
        },
        host: '0.0.0.0',
        port: 8001,
        watch: {
            disableGlobbing: false
        }
    }
})
