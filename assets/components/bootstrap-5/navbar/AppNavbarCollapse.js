import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppNavbarCollapse(props, context) {
    return h(
        'div',
        {class: 'collapse navbar-collapse'},
        h('ul', {class: 'me-auto navbar-nav'}, useSlots(context))
    )
}
