import type {FunContext, FunProps} from '../../../types/vue'
import type {VNode} from 'vue'
import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppCard(props: FunProps, context: FunContext): VNode {
    return h('div', {class: 'card'}, h('div', {class: 'card-body'}, useSlots(context)))
}
