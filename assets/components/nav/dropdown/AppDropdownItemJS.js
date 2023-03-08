import {generateVariant} from '../../props'
import {h} from 'vue'

function AppDropdownItemJS(props, context) {
    let css = 'dropdown-item'
    if (props.disabled)
        css += ' disabled'
    return h(
        'li',
        {class: `bg-${props.variant} text-dark`},
        h('span', {class: css}, context.slots['default']())
    )
}

AppDropdownItemJS.props = {disabled: {type: Boolean}, variant: generateVariant('primary')}

export default AppDropdownItemJS
