import './app.scss'
import './fortawesome'
import App from './components/App.vue'
import AppBtn from './components/AppBtn'
import AppContainer from './components/AppContainer'
import AppOverlay from './components/AppOverlay'
import Fa from './components/Fa'
import clone from 'lodash.clonedeep'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppContainer', AppContainer)
    .component('AppOverlay', AppOverlay)
    .component('Fa', Fa)
    .use(createPinia().use(({store}) => {
        if (store.setup) {
            const state = clone(store.$state)
            store.$reset = () => store.$patch(clone(state))
        }
    }))
useUser().fetch().then(() => app.use(router).mount('#vue'))
