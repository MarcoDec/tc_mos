import {h} from 'vue'

function AppOverlay(props, context) {
    let overlay = null
    const children = [context.slots['default']()]
    if (props.spinner) {
        overlay = {class: 'opacity-75 position-relative'}
        children.push(h(
            'div',
            {class: 'position-absolute start-50 top-50'},
            h('div', {class: 'spinner-border', role: 'status'})
        ))
    }
    return h('div', overlay, children)
}

AppOverlay.props = {spinner: {type: Boolean}}

export default AppOverlay
