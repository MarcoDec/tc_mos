import {createRouter, createWebHistory} from 'vue-router'
import AppHome from '../components/pages/AppHome'
import AppLogin from '../components/pages/AppLogin.vue'
import hr from './hr'
import logistics from './logistics'
import management from './management'
import production from './production'
import project from './project'
import purchase from './purchase'
import quality from './quality'
import selling from './selling'
import useUser from '../stores/security'
// import component from './routes/component'
// import customer from './routes/customer'
// import employee from './routes/employee'
// import equipment from './routes/equipment'
// import product from './routes/product'
// import supplier from './routes/supplier'
import direction from './routes/direction'

const router = createRouter({
    history: createWebHistory(), routes: [
        ...hr,
        ...logistics,
        ...management,
        ...production,
        ...project,
        ...purchase,
        ...quality,
        // ...supplier,
        // ...component,
        // ...customer,
        // ...product,
        // ...employee,
        // ...equipment,
        ...selling,
        ...direction,
        {component: AppLogin, meta: {title: 'Connexion — T-Concept GPAO'}, name: 'login', path: '/login'},
        {component: AppHome, meta: {title: 'T-Concept GPAO'}, name: 'home', path: '/'},
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
