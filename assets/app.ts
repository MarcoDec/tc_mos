import './app.scss'
import * as Cookies from './cookie'
import * as components from './components'
import type {Component} from 'vue'
import {createApp, defineAsyncComponent} from 'vue'
import App from './routing/App.vue'
import type {State} from './store/security'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {fetchApi} from './api'
import {generateStore} from './store'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing/router'

library.add(fas)
export const app = createApp(App as Component)
    .component('Fa', defineAsyncComponent(async () => import('@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon')))
for (const [name, component] of Object.entries(components))
    app.component(name, component)

async function mount(): Promise<void> {
    console.debug(app.config.globalProperties)
    if (app.config.globalProperties.mounted)
        return

    console.debug('call mounted')
    app.config.globalProperties.mounted = true
    const state: State = {password: null, username: null}
    if (Cookies.has()) {
        const id = Cookies.get('id')
        if (typeof id !== 'undefined') {
            const user = await fetchApi('/api/employees/{id}', {params: {id}})
            if (typeof user.username !== 'undefined')
                state.username = user.username
        }
    }
    app
        .use(generateStore(state))
        .use(router)
        .mount('#vue')
}

// eslint-disable-next-line @typescript-eslint/no-floating-promises
mount()
