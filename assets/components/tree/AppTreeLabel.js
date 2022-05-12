import {h, resolveComponent} from 'vue'

function AppTreeLabel(props, context) {
    return h('span', {class: 'pointer'}, [
        typeof context.slots['default'] === 'function' ? context.slots['default']() : h('span', {class: 'pe-4'}),
        h(resolveComponent('Fa'), {class: 'me-1', icon: 'layer-group'}),
        props.item.name
    ])
}

AppTreeLabel.props = {item: {required: true, type: Object}}

export default AppTreeLabel
