import {h} from 'vue'

function AppContainer(props, context) {
    return h('div', {class: props.fluid ? 'container-fluid' : 'container'}, context.slots['default']())
}

AppContainer.props = {fluid: {type: Boolean}}

export default AppContainer
