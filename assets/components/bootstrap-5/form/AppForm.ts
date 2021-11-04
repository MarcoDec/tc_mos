import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

export default function AppForm(): VNode {
    return h('form', {autocomplete: 'off', method: 'post'}, [
        h(resolveComponent('AppBtn'), {class: 'float-end', type: 'submit'})
    ])
}
