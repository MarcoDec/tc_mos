import checker from 'vite-plugin-checker'
import {defineConfig} from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    base: '/build/',
    build: {
        assetsDir: '',
        emptyOutDir: true,
        manifest: true,
        outDir: './public/build/',
        rollupOptions: {
            input: {index: './assets/index.js'},
            output: {
                // eslint-disable-next-line consistent-return
                manualChunks(id) {
                    if (id.includes('AppSuspenseWrapper') || id.includes('stores/options'))
                        return 'vendor'
                }
            }
        }
    },
    plugins: [
        symfonyPlugin(),
        vue(),
        checker({eslint: {lintCommand: 'eslint -c .eslintrc.js .eslintrc.js vite.config.js ./assets/**/*.{js,vue}'}})
    ],
    root: './',
    server: {
        force: true,
        fs: {allow: ['..'], strict: false},
        host: '0.0.0.0',
        port: 8001,
        watch: {disableGlobbing: false}
    }
})
