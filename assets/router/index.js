import {createRouter, createWebHistory} from 'vue-router'
import AppHome from './pages/AppHome'
import AppLogin from './pages/AppLogin.vue'
import hr from './routes/hr'
import logistics from './routes/logistics'
import management from './routes/management'
import production from './routes/production'
import project from './routes/project'
import purchase from './routes/purchase'
import quality from './routes/quality'
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
        {
            component: async () => import('./pages/AppProductionPlanning.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-schedule',
            path: '/manufacturingSchedule',
            props: {
                fields: [

                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Ind.',
                        name: 'ind',
                        type: 'text'
                    },
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Stocks',
                        name: 'stocks',
                        type: 'text'
                    },
                    {
                        label: '3%VP',
                        name: 'vp',
                        type: 'text'
                    },
                    {
                        label: 'Retard',
                        name: 'retard',
                        type: 'text'
                    }
                ],
                icon: 'table-list',
                title: 'Planning de production'
            }
        },
        ...hr,
        ...logistics,
        ...management,
        ...production,
        ...project,
        ...purchase,
        ...quality
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
