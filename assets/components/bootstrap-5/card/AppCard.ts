import type {ComponentPropsOptions, SetupContext} from 'vue'
import type {VNode} from '@vue/runtime-core'
import {h} from 'vue'

export default (props: ComponentPropsOptions, context: SetupContext): VNode => {
    const slot = context.slots.default
    const children = []
    if (slot)
        children.push(h('div', {class: 'card-body'}, slot()))
    return h('div', {class: 'card'}, children)
}
