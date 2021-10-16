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
import App from './routing/pages/App'
import type {AxiosError} from 'axios'
import VueAxios from 'vue-axios'
import axios from 'axios'
import {createApp} from 'vue'
import mitt from 'mitt'
import router from './routing/router'
import {store} from './store/store'

const app = createApp(App)

app
    .use(VueAxios, axios)
    .use(router)
    .use(store)

app.config.globalProperties.mitt = mitt()
app.config.globalProperties.axios.interceptors.response.use(
    (response: unknown) => response,
    (error: AxiosError): void => {
        app.config.globalProperties.mitt.emit('error', error)
    }
)

app
    .provide('axios', app.config.globalProperties.axios)
    .provide('mitt', app.config.globalProperties.mitt)

app
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

app.mount('#vue')
