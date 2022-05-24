import {createRouter, createWebHistory} from 'vue-router'
import AppAnalogClock from './pages/AppAnalogClock.vue'
import AppHome from './pages/AppHome'
import AppLogin from './pages/AppLogin.vue'
import {EmployeeRepository} from '../store/modules'
import project from './routes/project'
import purchase from './routes/purchase'
import store from '../store'

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
        ...project,
        ...purchase,
        {
            component: AppAnalogClock,
            meta: {requiresAuth: true},
            name: 'AppAnalogClock',
            path: '/appanalogclock'
        }

    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    if (to.matched.some(record => record.name === 'login') && store.$repo(EmployeeRepository).hasUser)
        return {name: 'home'}
    if (to.matched.some(record => record.meta.requiresAuth) && !store.$repo(EmployeeRepository).hasUser)
        return {name: 'login'}
})

export default router
