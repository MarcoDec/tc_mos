import './app.scss'
import * as components from './components'
import App from './routing/App'
import type {Component} from 'vue'
import {createApp} from 'vue'
import router from './routing/router'
import store from './store/store'

const app = createApp(App).use(router).use(store)
for (const [name, component] of Object.entries(components))
    app.component(name, component as Component)
app.mount('#vue')
