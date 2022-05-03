import {h} from 'vue'
import {useSlots} from '../../composition'

function AppAlert(props, context) {
    return h('div', {class: `alert alert-${props.variant}`, role: 'alert'}, useSlots(context))
}

AppAlert.displayName = 'AppAlert'
AppAlert.props = {variant: {default: 'danger', type: String}}

export default AppAlert
