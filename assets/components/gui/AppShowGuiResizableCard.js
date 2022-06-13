import {h, resolveComponent} from 'vue'

function AppShowGuiResizableCard(props, context) {
    return h(resolveComponent('AppShowGuiCard'), props, () => {
        const children = [h('hr', {
            class: 'resizer',
            onClick: () => props.gui.enableDrag(),
            onMousedown: () => props.gui.drag()
        })]
        if (typeof context.slots['default'] === 'function')
            children.push(context.slots['default']())
        return children
    })
}

AppShowGuiResizableCard.props = {
    gui: {required: true, type: Object},
    height: {required: true, type: String},
    marginEnd: {default: null, type: String},
    marginTop: {default: null, type: String},
    variant: {required: true, type: String},
    width: {required: true, type: String}
}

export default AppShowGuiResizableCard
