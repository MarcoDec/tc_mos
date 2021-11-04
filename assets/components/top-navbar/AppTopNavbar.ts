import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

export default function AppTopNavbar(): VNode {
    return h(
        resolveComponent('AppNavbar'),
        () => h(resolveComponent('AppNavbarBrand'), {href: '/'}, () => 'T-Concept')
    )
}
