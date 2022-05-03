import {h} from 'vue'
import {useSlots} from '../../composition'

function AppBadge(props, context) {
    let css = `badge bg-${props.variant}`
    if (props.tooltip) {
        css += ' rounded-pill start-100 top-0 translate-middle'
        if (!props.noAbsolute)
            css += ' position-absolute'
    }
    return h('span', {class: css}, useSlots(context))
}

AppBadge.displayName = 'AppBadge'
AppBadge.props = {noAbsolute: {type: Boolean}, tooltip: {type: Boolean}, variant: {default: 'danger', type: String}}

export default AppBadge
