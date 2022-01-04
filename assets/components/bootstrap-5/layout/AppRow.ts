import type {FunContext, FunProps} from '../../../types/vue'
import type {DeepReadonly} from '../../../types/types'
import type {VNode} from 'vue'
import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppRow(props: Readonly<FunProps>, context: DeepReadonly<FunContext>): VNode {
    return h('div', {class: 'row'}, useSlots(context))
}
