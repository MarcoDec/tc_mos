import {h} from 'vue'

function AppContainer(props, context) {
    const slot = context.slots['default']
    return h('div', {class: props.fluid ? 'container-fluid' : 'container'}, typeof slot === 'function' ? slot() : null)
}

AppContainer.props = {fluid: {type: Boolean}}

export default AppContainer
