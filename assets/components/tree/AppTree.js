import AppTreeForm from './AppTreeForm.vue'
import AppTreeNode from './AppTreeNode'
import {h} from 'vue'

function AppTree(props) {
    return h('div', {class: 'row', id: props.id}, [
        h(
            'div',
            {class: 'col'},
            props.items.map(item => h(AppTreeNode, {item, key: item['@id'], machine: props.machine}))
        ),
        h(AppTreeForm, {class: 'col', id: `${props.id}-form`, machine: props.machine})
    ])
}

AppTree.props = {
    id: {required: true, type: String},
    items: {required: true, type: Array},
    machine: {required: true, type: Object}
}

export default AppTree
