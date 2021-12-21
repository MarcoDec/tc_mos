import './app.scss'
import * as components from './components'
import {createApp, defineAsyncComponent} from 'vue'
import App from './routing/App.vue'
import type {Component} from 'vue'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing/router'
import store from './store/index'


library.add(fas)
const app = createApp(App as Component)
    .use(router)
    .use(store)
    .component('Fa', defineAsyncComponent(async () => import('@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon')))
for (const [name, component] of Object.entries(components))
    app.component(name, component)
app.mount('#vue')



