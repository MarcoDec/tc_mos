import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'

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
    plugins: [vue()],
    server: {port: 8001},
    root: './assets'
})
