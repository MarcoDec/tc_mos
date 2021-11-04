import './app.scss'
import * as components from './components'
import App from './routing/App'
import type {Component} from 'vue'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {createApp} from 'vue'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing/router'
import store from './store/index'

library.add(fas)

const app = createApp(App)
    .use(router)
    .use(store)
    .component('Fa', FontAwesomeIcon)
for (const [name, component] of Object.entries(components))
    app.component(name, component as Component)
app.mount('#vue')
