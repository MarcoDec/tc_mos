import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

export default function App(): VNode[] {
    return [
        h(resolveComponent('AppTopNavbar')),
        h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))
    ]
}
