import {h, resolveComponent} from 'vue'

function AppTreeLabel(props, context) {
    const attrs = {onClick: () => props.item.focus()}
    if (props.item.selected)
        attrs['class'] = 'bg-warning'
    return h(
        'span',
        attrs,
        [
            typeof context.slots['default'] === 'function' ? context.slots['default']() : h('span', {class: 'pe-2'}),
            h(resolveComponent('Fa'), {class: 'me-1', icon: 'layer-group'}),
            props.item.name
        ]
    )
}

AppTreeLabel.props = {item: {required: true, type: Object}}

export default AppTreeLabel
