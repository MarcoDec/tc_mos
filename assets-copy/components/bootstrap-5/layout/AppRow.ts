import type {FunContext, FunProps} from '../../../types/vue'
import type {VNode} from 'vue'
import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppRow(props: Readonly<FunProps>, context: FunContext): VNode {
    return h('div', {class: 'row'}, useSlots(context))
}
