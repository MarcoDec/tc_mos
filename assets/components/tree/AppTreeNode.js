import {h, resolveComponent} from 'vue'
import AppTreeVertex from './AppTreeVertex'

function AppTreeNode(props) {
    const children = [h(
        props.item.hasChildren ? AppTreeVertex : resolveComponent('AppTreeLabel'),
        {class: 'pointer', item: props.item}
    )]
    if (props.item.opened)
        for (const child of props.item.children)
            children.push(h(AppTreeNode, {class: 'ms-4', item: child, key: child.id}))
    return h('div', children)
}

AppTreeNode.props = {item: {required: true, type: Object}}

export default AppTreeNode
