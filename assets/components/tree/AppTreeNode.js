import AppTreeLabel from './AppTreeLabel'
import {h} from 'vue'

function AppTreeNode(props) {
    const children = [h(AppTreeLabel, {item: props.item})]
    for (const child of props.item.children)
        children.push(h(AppTreeNode, {class: 'ms-4', item: child, key: child.id}))
    return h('div', children)
}

AppTreeNode.props = {item: {required: true, type: Object}}

export default AppTreeNode
