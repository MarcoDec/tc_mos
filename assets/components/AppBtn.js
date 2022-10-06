import {h, resolveComponent} from 'vue'

function AppBtn(props, context) {
    const attrs = {disabled: props.disabled, type: props.type}
    const children = []
    let css = `btn btn-${props.variant}`
    if (props.icon) {
        attrs.title = props.label
        children.push(h(resolveComponent('Fa'), {icon: props.icon}))
        css += ' btn-icon'
    } else {
        children.push(props.label)
        css += ' btn-sm'
    }
    attrs['class'] = css
    if (typeof context.slots['default'] === 'function')
        children.push(context.slots['default']())
    return h('button', attrs, children)
}

AppBtn.props = {
    disabled: {type: Boolean},
    icon: {default: null, type: String},
    label: {required: true, type: String},
    type: {default: 'button', type: String},
    variant: {default: 'primary', type: String}
}

export default AppBtn
