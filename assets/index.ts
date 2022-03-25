import './app.scss'
import * as Cookies from './cookie'
import * as components from './components'
import type {State} from './store/security'
import app from './app'
import emitter from './emitter'
import {fab} from '@fortawesome/free-brands-svg-icons'
import {fas} from '@fortawesome/free-solid-svg-icons'
import fetchApi from './api'
import {generateStore} from './store'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing/router'

library.add(fab)
library.add(fas)

app.provide('emitter', emitter)
for (const [name, component] of Object.entries(components))
    app.component(name, component)

async function mount(): Promise<void> {
    const security: State = {
        '@context': 'null',
        '@id': '0',
        '@type': 'null',
        id: 0,
        name: 'null',
        roles: [],
        token: 'null'
    }
    if (Cookies.has()) {
        const id = Cookies.get('id')
        if (typeof id === 'string')
            try {
                const user = await fetchApi('/api/employees/{id}', 'get', {id})
                if (typeof user.username === 'string') {
                    security.roles = user.roles
                    security.username = user.username
                }
                // eslint-disable-next-line no-empty
            } catch (e) {
            }
        if (typeof security.username === 'undefined')
            Cookies.remove()
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
