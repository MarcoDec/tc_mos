import {createRouter, createWebHistory} from 'vue-router'
import AppLogin from '../components/pages/AppLogin'
import useUser from '../stores/security'

const router = createRouter({
    history: createWebHistory(), routes: [
        {
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: AppLogin,
            meta: {requiresAuth: false},
            name: 'login',
            path: '/login'
        }
    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    const user = useUser()
    if (to.matched.some(record => record.name === 'login') && user.isLogged)
        return {name: 'home'}
    if (to.matched.some(record => record.meta.requiresAuth) && !user.isLogged)
        return {name: 'login'}
})

export default router
