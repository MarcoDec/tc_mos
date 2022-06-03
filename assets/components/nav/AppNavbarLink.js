import {h, resolveComponent} from 'vue'
import {generateVariant} from '../props'

function AppNavbarLink(props, context) {
    return h(
        resolveComponent('AppDropdownItem'),
        {disabled: props.disabled, variant: 'none'},
        () => (props.disabled
            ? h(
                'span',
                {class: `text-${props.variant}`},
                [h(resolveComponent('Fa'), {icon: 'triangle-exclamation'}), context.slots['default']()]
            )
            : h(
                resolveComponent('AppRouterLink'),
                {css: `text-${props.variant}`, to: props.to},
                () => [
                    h(resolveComponent('Fa'), {brands: props.brands, icon: props.icon}),
                    context.slots['default']()
                ]
            ))
    )
}

AppNavbarLink.props = {
    brands: {type: Boolean},
    disabled: {type: Boolean},
    icon: {required: true, type: String},
    to: {required: true, type: String},
    variant: generateVariant('white')
}

export default AppNavbarLink
