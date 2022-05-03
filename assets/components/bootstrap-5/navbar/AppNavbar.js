import {h, resolveComponent} from 'vue'
import {useSlots} from '../../../composition'

function AppNavbar(props, context) {
    return h(
        'nav',
        {class: `bg-${props.variant} mb-2 navbar navbar-expand-lg navbar-${props.variant}`},
        h(resolveComponent('AppContainer'), {fluid: true}, () => useSlots(context))
    )
}

AppNavbar.displayName = 'AppNavbar'
AppNavbar.props = {variant: {default: 'dark', type: String}}

export default AppNavbar
