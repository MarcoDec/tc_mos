import {h, resolveComponent} from 'vue'
import {useSlots} from '../../../composition'

function AppNavbarBrand(props, context) {
    return h(resolveComponent('AppRouterLink'), {class: 'navbar-brand', to: props.to}, () => useSlots(context))
}

AppNavbarBrand.displayName = 'AppNavbarBrand'
AppNavbarBrand.props = {to: {required: true, type: String}}

export default AppNavbarBrand
