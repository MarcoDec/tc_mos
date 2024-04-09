import {generateVariant} from './props'
import {h} from 'vue'

function AppCardJS(props, context) {
    return h('div', {class: `bg-${props.variant} card`}, [
        h('h1', {class: 'card-title'}, props.title),
        h('div', {class: 'card-body'}, context.slots.default())
    ])
}

AppCardJS.props = {title: {required: true, type: String}, variant: generateVariant('primary')}

export default AppCardJS
