import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import checker from 'vite-plugin-checker'

export default defineConfig({
    base: '/build/',
    build: {
        assetsDir: '',
        manifest: true,
        outDir: '../public/build',
        rollupOptions: {
            input: {app: './assets/app.ts'},
            output: {manualChunks: undefined}
        }
    },
    plugins: [
        checker({vueTsc: true}),
        vue()
    ],
    server: {port: 8001},
    root: './assets'
})
