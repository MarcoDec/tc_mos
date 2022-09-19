import './app.scss'
import App from './components/App'
import AppContainer from './components/AppContainer'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppContainer', AppContainer)
    .use(createPinia())
useUser().fetch().then(() => app.use(router).mount('#vue'))
