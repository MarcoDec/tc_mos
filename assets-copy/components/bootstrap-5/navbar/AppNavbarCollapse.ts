import type {FunContext, FunProps} from '../../../types/vue'
import type {VNode} from 'vue'
import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppNavbarCollapse(props: FunProps, context: FunContext): VNode {
    return h(
        'div',
        {class: 'collapse navbar-collapse'},
        h('ul', {class: 'me-auto navbar-nav'}, useSlots(context))
    )
}
