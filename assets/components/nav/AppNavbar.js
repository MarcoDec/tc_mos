import {h, resolveComponent} from 'vue'
import {generateVariant} from '../validators'

function AppNavbar(props) {
    return h(
        'nav',
        {class: `bg-${props.variant} mb-1 navbar navbar-${props.variant}`},
        h(
            resolveComponent('AppContainer'),
            {fluid: true},
            () => h('span', {class: 'm-0 p-0 navbar-brand'}, 'T-Concept')
        )
    )
}

AppNavbar.props = {variant: generateVariant('dark')}

export default AppNavbar
