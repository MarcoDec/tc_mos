import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppDropdownItem(props, context) {
    return h('li', {class: `bg-${props.variant} dropdown-item text-dark`}, useSlots(context))
}

AppDropdownItem.displayName = 'AppDropdownItem'
AppDropdownItem.props = {variant: {default: 'dark', type: String}}

export default AppDropdownItem
