import './app.scss'
import App from './App'
import AppContainer from './components/layout/AppContainer'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'

createApp(App)
    .component('AppContainer', AppContainer)
    .use(createPinia())
    .use(router)
    .mount('#vue')
