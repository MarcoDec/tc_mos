import {createRouter, createWebHistory} from 'vue-router'
import AppHome from './pages/AppHome'
import AppLogin from './pages/AppLogin.vue'
import management from './routes/management'
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
        ...management,
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
