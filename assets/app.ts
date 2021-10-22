import './app.scss'
import {
    AppAlert,
    AppBtn,
    AppCard,
    AppCol,
    AppContainer,
    AppForm,
    AppFormGroup,
    AppInput,
    AppLabel,
    AppModal,
    AppModalError,
    AppNavbar,
    AppNavbarBrand,
    AppNavbarCollapse,
    AppNavbarNav,
    AppNavbarText,
    AppRow,
    AppTopNavbar,
    AppTopNavbarUser
} from './components'
import App from './routing/App'
import {FontAwesomeIcon} from './fontawesome'
import {createApp} from 'vue'
import {emitter} from './api'
import manager from './store/repository/RepositoryManager'
import router from './routing/router'
import store from './store/store'

createApp(App)
    .use(router)
    .use(store)
    .provide('mitt', emitter)
    .provide('repositories', manager)
    .component('AppAlert', AppAlert)
    .component('AppBtn', AppBtn)
    .component('AppCard', AppCard)
    .component('AppCol', AppCol)
    .component('AppContainer', AppContainer)
    .component('AppForm', AppForm)
    .component('AppFormGroup', AppFormGroup)
    .component('AppInput', AppInput)
    .component('AppLabel', AppLabel)
    .component('AppModal', AppModal)
    .component('AppModalError', AppModalError)
    .component('AppNavbar', AppNavbar)
    .component('AppNavbarBrand', AppNavbarBrand)
    .component('AppNavbarCollapse', AppNavbarCollapse)
    .component('AppNavbarNav', AppNavbarNav)
    .component('AppNavbarText', AppNavbarText)
    .component('AppRow', AppRow)
    .component('AppTopNavbar', AppTopNavbar)
    .component('AppTopNavbarUser', AppTopNavbarUser)
    .component('FontAwesomeIcon', FontAwesomeIcon)
    .mount('#vue')
