import {generateVariant} from './props'
import {h} from 'vue'

function AppCard(props, context) {
    return h('div', {class: `bg-${props.variant} card`}, [
        Array.isArray(props.title)
            ? h('div', {class: 'card-title row'}, props.title.map(title => h('h1', {class: 'col'}, title)))
            : h('h1', {class: 'card-title'}, props.title),
        h('div', {class: 'card-body'}, context.slots['default']())
    ])
}

AppCard.props = {title: {required: true, type: [Array, String]}, variant: generateVariant('primary')}

export default AppCard
