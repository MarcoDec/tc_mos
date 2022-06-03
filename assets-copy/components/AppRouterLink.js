import {h, resolveComponent} from 'vue'
import {useSlots} from '../composition'

function AppRouterLink(props, context) {
    const slots = useSlots(context)
    return props.disabled
        ? h('span', [...slots, h('Fa', {icon: 'triangle-exclamation'})])
        : h(resolveComponent('RouterLink'), {to: {name: props.to}}, () => slots)
}

AppRouterLink.displayName = 'AppRouterLink'
AppRouterLink.props = {disabled: {type: Boolean}, to: {required: true, type: String}}

export default AppRouterLink
