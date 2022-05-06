import {h, resolveComponent} from 'vue'
import AppNavbar from './libs/bootstrap-5/nav/AppNavbar'

export default function App() {
    return [h(AppNavbar), h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))]
}
