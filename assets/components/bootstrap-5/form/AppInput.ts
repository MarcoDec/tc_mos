import type {VNode} from '@vue/runtime-core'
import {h} from 'vue'

export default function AppInput(props: Record<string, unknown>): VNode {
    return h('input', {class: 'form-control', ...props})
}
