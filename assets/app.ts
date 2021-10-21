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
    AppRow
} from './components'
import App from './routing/App'
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
    .component('AppRow', AppRow)
    .mount('#vue')
