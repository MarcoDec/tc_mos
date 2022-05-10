import './app.scss'
import App from './App'
import AppContainer from './components/layout/AppContainer'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUserStore from './stores/hr/employee/user'

const app = createApp(App).component('AppContainer', AppContainer).use(createPinia())

async function fetchUser() {
    await useUserStore().fetchUser()
}

fetchUser().then(() => app.use(router).mount('#vue'))
