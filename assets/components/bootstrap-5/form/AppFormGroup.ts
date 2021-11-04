import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

export default function AppFormGroup(): VNode {
    return h(resolveComponent('AppRow'), {class: 'mb-3'}, [
        h(resolveComponent('AppLabel')),
        h(resolveComponent('AppCol'), h(resolveComponent('AppInput')))
    ])
}
