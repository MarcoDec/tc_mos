import {h, resolveComponent} from 'vue'

function AppTreeVertex(props) {
    return h(
        resolveComponent('AppTreeLabel'),
        {item: props.item},
        () => h(resolveComponent('Fa'), {class: 'me-1', icon: `chevron-${props.item.opened ? 'up' : 'down'}`})
    )
}

AppTreeVertex.props = {item: {required: true, type: Object}}

export default AppTreeVertex
