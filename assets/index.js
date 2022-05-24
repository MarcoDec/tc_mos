import './app.scss'
import App from './App'
import AppBtn from './components/AppBtn'
import AppCard from './components/AppCard'
import AppContainer from './components/layout/AppContainer'
import AppDropdownItem from './components/nav/AppDropdownItem'
import AppForm from './components/form/AppForm'
import AppInput from './components/form/field/input/AppInput'
import AppInputGuesser from './components/form/field/input/AppInputGuesser'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/AppRouterLink'
import AppTableFormField from './components/table/AppTableFormField'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm'
import AppTreeLabel from './components/tree/AppTreeLabel'
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
    .component('AppCard', AppCard)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppOverlay', AppOverlay)
    .component('AppRouterLink', AppRouterLink)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTreeLabel', AppTreeLabel)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetch()
}

fetchUser().then(() => app.use(router).mount('#vue'))
