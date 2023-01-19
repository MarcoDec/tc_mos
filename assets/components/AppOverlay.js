import {h} from 'vue'

function AppOverlay(props, context) {
    let overlay = null
    const children = []
    if (typeof context.slots['default'] === 'function')
        children.push(context.slots['default']())
    if (props.spinner) {
        overlay = {class: 'opacity-75 position-relative'}
        children.push(h(
            props.tag,
            {class: 'position-absolute start-50 top-50'},
            h(props.tag, {class: 'spinner-border', role: 'status'})
        ))
    }
    return h(props.tag, overlay, children)
}

AppOverlay.props = {spinner: {type: Boolean}, tag: {default: 'div', type: String}}

export default AppOverlay
