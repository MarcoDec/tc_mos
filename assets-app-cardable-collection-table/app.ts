/* eslint-disable @typescript-eslint/no-unsafe-argument */
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
import AppCollectionTable from './components/bootstrap-5/app-collection-table/AppCollectionTable.vue'
import type {AxiosError} from 'axios'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import VueAxios from 'vue-axios'
import axios from 'axios'
import {createApp} from 'vue'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {library} from '@fortawesome/fontawesome-svg-core'
import mitt from 'mitt'
import router from './routing/router'
import {store} from './store/store'

library.add(fas)
const app = createApp(App)

app
    .use(VueAxios, axios)
    .use(router)
    .use(store)

app.config.globalProperties.mitt = mitt()
app.config.globalProperties.axios.interceptors.response.use(
    (response: unknown) => response,
    (error: AxiosError): void => {
        if (error.response?.status !== 401)
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
    .component('AppCollectionTable', AppCollectionTable)
    .component('Fa', FontAwesomeIcon)

app.mount('#vue')
