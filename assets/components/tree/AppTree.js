import AppTreeNode from './AppTreeNode'
import {h} from 'vue'

function AppTree(props) {
    return h(
        'div',
        {class: 'row'},
        h('div', {class: 'col'}, props.items.map(item => h(AppTreeNode, {item, key: item.id})))
    )
}

AppTree.props = {items: {required: true, type: Array}}

export default AppTree
