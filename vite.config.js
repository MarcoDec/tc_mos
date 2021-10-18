import {existsSync, unlinkSync} from 'fs'
import {defineConfig} from 'vite'
import {resolve} from 'path'
import checker from 'vite-plugin-checker'
import vue from '@vitejs/plugin-vue'

/* if you're using React */
// import reactRefresh from "@vitejs/plugin-react-refresh";

const symfonyPlugin = {
    configResolved(config) {
        if (config.env.DEV && config.build.manifest) {
            const buildDir = resolve(
                config.root,
                config.build.outDir,
                'manifest.json'
            )
            existsSync(buildDir) && unlinkSync(buildDir)
        }
    },
    configureServer(devServer) {
        const {watcher, ws} = devServer
        watcher.add(resolve('templates/**/*.twig'))
        watcher.on('change', path => {
            if (path.endsWith('.twig')) {
                ws.send({
                    type: 'full-reload'
                })
            }
        })
    },
    name: 'symfony'
}

export default defineConfig({
    base: '/build/',
    build: {
        assetsDir: '',
        emptyOutDir: true,
        manifest: true,
        outDir: '../public/build/',
        rollupOptions: {
            input: ['./assets/app.ts']
        }
    },
    plugins: [
        /* reactRefresh(), // if you're using React */
        symfonyPlugin,
        vue(),
        checker({
            eslint: {extensions: ['.ts', '.vue'], files: [resolve('assets')]},
            typescript: true,
            vueTsc: true
        })
    ],
    root: './assets',
    server: {
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
