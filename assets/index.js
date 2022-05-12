import './app.scss'
import App from './App'
import AppBtn from './components/AppBtn'
import AppContainer from './components/layout/AppContainer'
import AppDropdownItem from './components/nav/AppDropdownItem'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/AppRouterLink'
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
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppOverlay', AppOverlay)
    .component('AppRouterLink', AppRouterLink)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetch()
}

fetchUser().then(() => app.use(router).mount('#vue'))
