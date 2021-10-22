import type {SetupContext} from 'vue'
import type {VNode} from '@vue/runtime-core'
import {h} from 'vue'

export default function AppNavbarNav(props: Record<string, unknown>, context: Omit<SetupContext, 'expose'>): VNode {
    const children = []
    const slot = context.slots.default
    if (typeof slot !== 'undefined')
        children.push(slot())
    return h('div', {class: 'navbar-nav', ...props}, children)
}
