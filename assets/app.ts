import 'bootstrap/dist/css/bootstrap.css'
import App from './routing/pages/App'
import {createApp} from 'vue'
import router from './routing/router'
import store from './store/store'

createApp(App)
    .component('AppCard', async() => import('./components/bootstrap-5/card/AppCard'))
    .component('AppForm', async() => import('./components/bootstrap-5/form/AppForm.vue'))
    .component('AppFormGroup', async() => import('./components/bootstrap-5/form/AppFormGroup.vue'))
    .use(store)
    .use(router)
    .mount('#vue')
