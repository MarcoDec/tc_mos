import {h, resolveComponent} from 'vue'

function AppBtn(props) {
    const attrs = {disabled: props.disabled, type: props.type}
    let children = null
    let css = `btn btn-${props.variant}`
    if (props.icon) {
        attrs.title = props.label
        children = h(resolveComponent('Fa'), {icon: props.icon})
        css += ' btn-icon'
    } else {
        children = props.label
        css += ' btn-sm'
    }
    attrs['class'] = css
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
