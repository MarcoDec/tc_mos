import {createRouter, createWebHistory} from 'vue-router'
import AppHome from '../components/pages/AppHome'
import AppLogin from '../components/pages/AppLogin.vue'
import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'
import useUser from '../stores/security'

const router = createRouter({
    history: createWebHistory(), routes: [
        {component: AppHome, meta: {title: 'T-Concept GPAO'}, name: 'home', path: '/'},
        {
            component: AppTablePageSuspense,
            meta: {title: 'Messages TVA — T-Concept GPAO'},
            name: 'vat-messages',
            path: '/vat-messages',
            props: {
                fields: readonly([{create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true}]),
                icon: 'comments-dollar',
                title: 'Messages TVA'
            }
        },
        {component: AppLogin, meta: {title: 'Connexion — T-Concept GPAO'}, name: 'login', path: '/login'},
        {meta: {title: 'T-Concept GPAO'}, name: 'all', path: '/:pathMatch(.*)*'}
    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    const user = useUser()
    if (
        to.matched.some(record => record.name === 'login') && user.isLogged
        || to.matched.some(record => record.name === 'all')
    )
        return {name: 'home'}
    if (to.matched.some(record => record.name !== 'login') && !user.isLogged)
        return {name: 'login'}
})

export default router
