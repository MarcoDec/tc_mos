import {h, resolveComponent} from 'vue'
import type {VNode} from '@vue/runtime-core'

export default function App(): VNode[] {
    return [
        h(
            resolveComponent('AppNavbar'),
            () => h(resolveComponent('AppNavbarBrand'), {href: '/', title: 'T-Concept'})
        ),
        h(resolveComponent('AppContainer'), () => h(resolveComponent('RouterView')))
    ]
}
