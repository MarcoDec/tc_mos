import {createRouter, createWebHistory} from 'vue-router'
import AppLogin from './pages/AppLogin.vue'

export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: AppLogin,
            name: 'login',
            path: '/'
        }
    ]
})
