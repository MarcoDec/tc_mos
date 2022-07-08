import {createRouter, createWebHistory} from 'vue-router'
import AppHome from './pages/AppHome'
import AppLogin from './pages/AppLogin.vue'
import component from './routes/component'
import customer from './routes/customer'
import employee from './routes/employee'
import hr from './routes/hr'
import logistics from './routes/logistics'
import management from './routes/management'
import manufacturingOrder from './routes/manufacturingOrder'
import product from './routes/product'
import production from './routes/production'
import project from './routes/project'
import purchase from './routes/purchase'
import quality from './routes/quality'
import supplier from './routes/supplier'
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
        ...component,
        ...customer,
        ...employee,
        ...hr,
        ...logistics,
        ...management,
        ...manufacturingOrder,
        ...product,
        ...production,
        ...project,
        ...purchase,
        ...quality,
        ...supplier
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
