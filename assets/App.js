import {h, resolveComponent} from 'vue'
import AppNavbar from './components/nav/AppNavbar.vue'
import {useRoute} from 'vue-router'

export default function App() {
    const route = useRoute()
    return [
        h(AppNavbar),
        h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView'), {key: route.name}))
    ]
}
