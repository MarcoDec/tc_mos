import './app.scss'
import * as Cookies from './cookie'
import * as components from './components'
import {createApp, defineAsyncComponent} from 'vue'
import App from './routing/App.vue'
import type {Component} from 'vue'
import type {State} from './store/security'
import emitter from './emitter'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {fetchApi} from './api'
import {generateStore} from './store'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing/router'
import app from './app'

library.add(fas)
 app
    .provide('emitter', emitter)
    .component('Fa', defineAsyncComponent(async () => import('@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon')))
for (const [name, component] of Object.entries(components))
    app.component(name, component)

async function mount(): Promise<void> {
    const security: State = {username: null}
    if (Cookies.has()) {
        const id = Cookies.get('id')
        if (typeof id !== 'undefined') {
            const user = await fetchApi('/api/employees/{id}', {params: {id}})
            if (typeof user.username !== 'undefined')
                security.username = user.username
        }
    }
    const store = generateStore(security)
    app.use(store)

    // eslint-disable-next-line consistent-return
    router.beforeEach(to => {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/strict-boolean-expressions
        if (to.matched.some(record => record.meta.requiresAuth && record.name !== 'login') && !store.getters['security/hasUser'])
            return {name: 'login'}
    })
}

// eslint-disable-next-line @typescript-eslint/no-floating-promises
mount().then(() => app.use(router).mount('#vue'))
