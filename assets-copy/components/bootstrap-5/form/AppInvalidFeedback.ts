import type {FunContext, FunProps} from '../../../types/vue'
import type {VNode} from 'vue'
import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppInvalidFeedback(props: FunProps, context: FunContext): VNode {
    return h('div', {class: 'invalid-feedback'}, useSlots(context))
}
