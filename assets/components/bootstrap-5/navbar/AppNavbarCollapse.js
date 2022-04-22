import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppNavbarCollapse(props, context) {
    return h(
        'div',
        {class: 'collapse navbar-collapse'},
        h('ul', {class: 'me-auto navbar-nav'}, useSlots(context))
    )
}

AppNavbarCollapse.displayName = 'AppNavbarCollapse'

export default AppNavbarCollapse
