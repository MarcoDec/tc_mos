import './style/app.scss'
import './style/fortawesome'
import App from './components/App.vue'
import AppBtn from './components/AppBtn'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppForm from './components/form/AppForm.vue'
import AppInputGuesser from './components/form/field/input/AppInputGuesser'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import AppSuspense from './components/AppSuspense.vue'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm.vue'
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
    .component('AppForm', AppForm)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppOverlay', AppOverlay)
    .component('AppRouterLink', AppRouterLink)
    .component('AppSuspense', AppSuspense)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('Fa', Fa)
    .use(createPinia().use(({store}) => {
        if (store.setup) {
            const state = clone(store.$state)
            store.$reset = () => store.$patch(clone(state))
        }
    }))
useUser().fetch().then(() => app.use(router).mount('#vue'))
