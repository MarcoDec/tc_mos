import {h, resolveComponent} from 'vue'
import {useSlots} from '../../../composition'

function AppNavbarLink(props, context) {
    return h(
        'li',
        h(
            resolveComponent('AppRouterLink'),
            {class: `dropdown-item text-${props.variant}`, disabled: props.disabled, to: props.to},
            () => [
                h(resolveComponent('Fa'), {brands: props.brands, icon: props.icon}),
                useSlots(context)
            ]
        )
    )
}

AppNavbarLink.displayName = 'AppNavbarLink'
AppNavbarLink.props = {
    brands: {type: Boolean},
    disabled: {type: Boolean},
    icon: {required: true, type: String},
    to: {required: true, type: String},
    variant: {default: 'white', type: String}
}

export default AppNavbarLink
