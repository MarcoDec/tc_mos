import AppContainer from '../AppContainer'
import {h} from 'vue'

export default function AppNavbar() {
    return h(
        'nav',
        {class: 'bg-dark mb-1 navbar navbar-dark navbar-expand-sm'},
        h(
            AppContainer,
            {fluid: true},
            () => h('span', {class: 'm-0 navbar-brand p-0'}, 'T-Concept')
        )
    )
}
