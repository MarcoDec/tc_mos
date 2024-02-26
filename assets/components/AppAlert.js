import {generateVariant} from './props'
import {h} from 'vue'

function AppAlert(props, context) {
    return h('div', {class: `alert alert-${props.variant}`, role: 'alert'}, context.slots.default())
}

AppAlert.props = {variant: generateVariant('danger')}

export default AppAlert
