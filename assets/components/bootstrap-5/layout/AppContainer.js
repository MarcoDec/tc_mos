import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppContainer(props, context) {
    return h('div', {class: props.fluid ? 'container-fluid' : 'container'}, useSlots(context))
}

AppContainer.displayName = 'AppContainer'
AppContainer.props = {fluid: {type: Boolean}}

export default AppContainer
