import {generateVariant} from './props'
import {h} from 'vue'

function AppBadge(props, context) {
    let css = `badge bg-${props.variant}`
    if (props.tooltip) {
        css += ' rounded-pill start-100 top-0 translate-middle'
        if (!props.noAbsolute)
            css += ' position-absolute'
    }
    return h('span', {class: css}, context.slots['default']())
}

AppBadge.displayName = 'AppBadge'
AppBadge.props = {noAbsolute: {type: Boolean}, tooltip: {type: Boolean}, variant: generateVariant('danger')}

export default AppBadge
