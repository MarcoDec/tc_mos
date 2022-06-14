import {h, resolveComponent} from 'vue'

function AppTreeVertex(props) {
    return h(
        resolveComponent('AppTreeLabel'),
        {item: props.item, machine: props.machine},
        () => h(resolveComponent('Fa'), {class: 'me-1', icon: `chevron-${props.item.opened ? 'up' : 'down'}`})
    )
}

AppTreeVertex.props = {item: {required: true, type: Object}, machine: {required: true, type: Object}}

export default AppTreeVertex
