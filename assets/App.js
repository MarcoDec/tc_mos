import {h, resolveComponent} from 'vue'
import AppNavbar from './components/nav/AppNavbar.vue'

export default function App() {
    return [h(AppNavbar), h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))]
}
