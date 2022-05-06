import {generateVariant} from './validators'
import {h} from 'vue'

function AppCard(props, context) {
    return h('div', {class: `bg-${props.variant} card`}, [
        h('h1', {class: 'card-title'}, props.title),
        h('div', {class: 'card-body'}, context.slots['default']())
    ])
}

AppCard.props = {title: {required: true, type: String}, variant: generateVariant('primary')}

export default AppCard
