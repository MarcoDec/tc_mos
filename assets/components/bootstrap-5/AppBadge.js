import {h} from 'vue'
import {useSlots} from '../../composition'

function AppBadge(props, context) {
    return h('span', {class: `badge bg-${props.variant}`}, useSlots(context))
}

AppBadge.displayName = 'AppBadge'
AppBadge.props = {variant: {default: 'danger', type: String}}

export default AppBadge
