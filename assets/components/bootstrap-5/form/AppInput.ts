import type {VNode} from 'vue'
import {h} from 'vue'

export default function AppInput(): VNode {
    return h('input', {class: 'form-control'})
}
