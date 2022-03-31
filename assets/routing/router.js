import {createRouter, createWebHistory} from 'vue-router'
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
            component: async () => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: async () => import('./pages/security/AppLogin.vue'),
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
