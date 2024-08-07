import checker from 'vite-plugin-checker'
import {defineConfig} from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import vue from '@vitejs/plugin-vue'
import dotenv from 'dotenv'

dotenv.config()

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
                    if (id.includes('fontawesome') || id.includes('fortawesome'))
                        return 'fontawesome'
                }
            }
        }
    },
    optimizeDeps: {force: true},
    plugins: [
        symfonyPlugin(),
        vue(),
        checker({eslint: {lintCommand: 'eslint -c .eslintrc.js .eslintrc.js vite.config.js ./assets/**/*.{js,vue}'}})
    ],
    define: {
        '__VUE_PROD_HYDRATION_MISMATCH_DETAILS__': false,
      },
    root: './',
    server: {
        fs: {allow: ['..'], strict: false},
        host: '0.0.0.0',
        port: 8001,
        watch: {disableGlobbing: false}
    }
})
