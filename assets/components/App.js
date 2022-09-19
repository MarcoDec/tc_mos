import {h, resolveComponent} from 'vue'
import AppNavbar from './nav/AppNavbar'

export default function App() {
    return [
        h(AppNavbar),
        h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))
    ]
}
