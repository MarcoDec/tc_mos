import {h, resolveComponent} from 'vue'
import type {VNode} from '@vue/runtime-core'

export default function App(): VNode[] {
    return [
        h(resolveComponent('AppModalError')),
        h(resolveComponent('AppTopNavbar')),
        h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))
    ]
}
