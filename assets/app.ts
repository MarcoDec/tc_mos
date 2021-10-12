import './app.scss'
import {createApp, defineAsyncComponent} from 'vue'
import App from './routing/pages/App'
import type {AxiosError} from 'axios'
import VueAxios from 'vue-axios'
import axios from 'axios'
import mitt from 'mitt'
import router from './routing/router'
import {store} from './store/store'

const app = createApp(App)
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

app.provide('axios', app.config.globalProperties.axios)
    .provide('mitt', app.config.globalProperties.mitt)
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppAlert', defineAsyncComponent(async () => import('./components/bootstrap-5/AppAlert.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppBtn', defineAsyncComponent(async () => import('./components/bootstrap-5/AppBtn.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppCard', defineAsyncComponent(async () => import('./components/bootstrap-5/card/AppCard.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppCol', defineAsyncComponent(async () => import('./components/bootstrap-5/layout/AppCol.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppContainer', defineAsyncComponent(async () => import('./components/bootstrap-5/layout/AppContainer.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppForm', defineAsyncComponent(async () => import('./components/bootstrap-5/form/AppForm.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppFormGroup', defineAsyncComponent(async () => import('./components/bootstrap-5/form/AppFormGroup.vue')))
    .component('AppInput', defineAsyncComponent(async () => import('./components/bootstrap-5/form/AppInput')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppLabel', defineAsyncComponent(async () => import('./components/bootstrap-5/form/AppLabel.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppModal', defineAsyncComponent(async () => import('./components/bootstrap-5/modal/AppModal.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppModalError', defineAsyncComponent(async () => import('./components/AppModalError.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppNavbar', defineAsyncComponent(async () => import('./components/bootstrap-5/navbar/AppNavbar.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppNavbarBrand', defineAsyncComponent(async () => import('./components/bootstrap-5/navbar/AppNavbarBrand.vue')))
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    .component('AppRow', defineAsyncComponent(async () => import('./components/bootstrap-5/layout/AppRow.vue')))
    .mount('#vue')
