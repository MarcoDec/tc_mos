import {h, resolveComponent} from 'vue'
import {generateVariant} from './props'

function AppBtn(props, context) {
    let css = `btn btn-${props.variant}`
    const children = []
    if (typeof context.slots['default'] === 'function')
        children.push(context.slots['default']())
    if (props.icon) {
        css += ' btn-icon'
        children.push(h(resolveComponent('Fa'), {icon: props.icon}))
    } else
        css += ' btn-sm'
    return h(
        'button',
        {class: css, disabled: props.disabled, type: props.type},
        children
    )
}

AppBtn.props = {
    disabled: {type: Boolean},
    icon: {default: null, type: String},
    type: {default: 'button', type: String},
    variant: generateVariant('primary')
}

export default AppBtn
