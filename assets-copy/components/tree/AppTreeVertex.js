import {h, resolveComponent} from 'vue'
import AppTreeLabel from './AppTreeLabel.vue'

function AppTreeVertex(props) {
    return h(
        AppTreeLabel,
        props,
        () => h(resolveComponent('Fa'), {class: 'me-2', icon: `chevron-${props.node.opened ? 'up' : 'down'}`})
    )
}

AppTreeVertex.displayName = 'AppTreeVertex'
AppTreeVertex.props = {node: {required: true, type: Object}}

export default AppTreeVertex
