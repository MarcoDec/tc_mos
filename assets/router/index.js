import {createRouter, createWebHistory} from 'vue-router'
import AppHome from './pages/AppHome'
import AppLogin from './pages/AppLogin.vue'
import logistics from './routes/logistics'
import management from './routes/management'
import production from './routes/production'
import project from './routes/project'
import purchase from './routes/purchase'
import useUserStore from '../stores/hr/employee/user'

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
        ...logistics,
        ...management,
        ...production,
        ...project,
        ...purchase
    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    const user = useUserStore()
    if (to.matched.some(record => record.name === 'login') && user.isLogged)
        return {name: 'home'}
    if (to.matched.some(record => record.meta.requiresAuth) && !user.isLogged)
        return {name: 'login'}
})

export default router
