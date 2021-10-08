import 'bootstrap/dist/css/bootstrap.css'
import App from './routing/pages/App'
import {createApp} from 'vue'
import router from './routing/router'
import {store} from './store/store'

createApp(App)
    .component('AppCard', async() => import('./components/bootstrap-5/card/AppCard'))
    .component('AppForm', async() => import('./components/bootstrap-5/form/AppForm.vue'))
    .use(router)
    .use(store)
    .mount('#vue')
