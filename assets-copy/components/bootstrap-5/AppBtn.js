import {h, resolveComponent} from 'vue'
import {useSlots} from '../../composition'

function AppBtn(props, context) {
    let css = `btn btn-${props.size} btn-${props.variant}`
    if (props.icon)
        css += ' btn-icon'
    let slots = useSlots(context)
    if (slots.length === 0)
        slots = props.icon === null ? null : h(resolveComponent('Fa'), {icon: props.icon})
    return h('button', {class: css, type: props.type}, slots)
}

AppBtn.displayName = 'AppBtn'
AppBtn.props = {
    icon: {default: null, type: String},
    size: {default: 'sm', type: String},
    type: {default: 'button', type: String},
    variant: {default: 'primary', type: String}
}

export default AppBtn
