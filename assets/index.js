import './app.scss'
import App from './App'
import AppBtn from './components/AppBtn'
import AppContainer from './components/layout/AppContainer'
import AppOverlay from './components/AppOverlay'
import Fa from './components/Fa'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import {fab} from '@fortawesome/free-brands-svg-icons'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './router'
import useUserStore from './stores/hr/employee/user'

library.add(fab)
library.add(fas)

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppContainer', AppContainer)
    .component('AppOverlay', AppOverlay)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetchUser()
}

fetchUser().then(() => app.use(router).mount('#vue'))
