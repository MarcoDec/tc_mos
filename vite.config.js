import {defineConfig} from 'vite'
import {resolve} from 'path'
import checker from 'vite-plugin-checker'
import vue from '@vitejs/plugin-vue'
import symfonyPlugin from 'vite-plugin-symfony'

export default defineConfig({
    base: '/build/',
    build: {
        assetsDir: '',
        emptyOutDir: true,
        manifest: true,
        outDir: '../public/build/',
        rollupOptions: {
            input: {index: './index.ts'}
        }
    },
    plugins: [
        symfonyPlugin(),
        vue(),
        checker({
            eslint: {extensions: ['.ts', '.vue'], files: [resolve('assets')]},
            typescript: true,
            vueTsc: true
        })
    ],
    root: './assets',
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
