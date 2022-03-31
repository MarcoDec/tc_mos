import {createRouter, createWebHistory} from 'vue-router'
import AppHome from './pages/AppHome'
import AppLogin from './pages/security/AppLogin.vue'
import hr from './routes/hr'
import it from './routes/it'
import logistics from './routes/logistics'
import management from './routes/management'
import production from './routes/production'
import project from './routes/project'
import purchase from './routes/purchase'
import quality from './routes/quality'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: AppHome,
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: AppLogin,
            meta: {requiresAuth: false},
            name: 'login',
            path: '/login'
        },
        ...hr,
        ...it,
        ...logistics,
        ...management,
        ...production,
        ...project,
        ...purchase,
        ...quality
    ]
})

export default router
