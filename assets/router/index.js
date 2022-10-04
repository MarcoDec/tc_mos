import {createRouter, createWebHistory} from 'vue-router'
import AppHome from '../components/pages/AppHome'
import AppLogin from '../components/pages/AppLogin.vue'
import useUser from '../stores/security'

const router = createRouter({
    history: createWebHistory(), routes: [
        {component: AppHome, meta: {title: 'T-Concept GPAO'}, name: 'home', path: '/'},
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
