import {generateVariant} from '../props'
import {h} from 'vue'

function AppDropdownItem(props, context) {
    let css = 'dropdown-item'
    if (props.disabled)
        css += ' disabled'
    return h(
        'li',
        {class: `bg-${props.variant} text-dark`},
        h('span', {class: css}, context.slots['default']())
    )
}

AppDropdownItem.props = {disabled: {type: Boolean}, variant: generateVariant('primary')}

export default AppDropdownItem
