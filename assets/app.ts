import 'bootstrap/dist/css/bootstrap.css'
import App from './routing/pages/App'
import {createApp} from 'vue'
import router from './routing/router'
import store from './store/store'

createApp(App)
    .use(store)
    .use(router)
    .mount('#vue')
