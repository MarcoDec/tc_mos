import './app.scss'
import * as components from './components'
import App from './routing/App.vue'
import {EmployeeRepository} from './store/modules'
import {createApp} from 'vue'
import {fab} from '@fortawesome/free-brands-svg-icons'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {library} from '@fortawesome/fontawesome-svg-core'
import router from './routing'
import store from './store'

library.add(fab)
library.add(fas)

const app = createApp(App)
for (const [name, component] of Object.entries(components))
    app.component(name, component)

async function initStore() {
    await store.$repo(EmployeeRepository).fetchUser('login')
    app.use(store)
}

initStore().then(() => app.use(router).mount('#vue'))
