import './app.scss'
import './fortawesome'
import App from './components/App.vue'
import AppBtn from './components/AppBtn'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import Fa from './components/Fa'
import clone from 'lodash.clonedeep'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppOverlay', AppOverlay)
    .component('AppRouterLink', AppRouterLink)
    .component('Fa', Fa)
    .use(createPinia().use(({store}) => {
        if (store.setup) {
            const state = clone(store.$state)
            store.$reset = () => store.$patch(clone(state))
        }
    }))
useUser().fetch().then(() => app.use(router).mount('#vue'))
