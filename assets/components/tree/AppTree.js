import AppTreeForm from './AppTreeForm.vue'
import AppTreeNode from './AppTreeNode'
import {h} from 'vue'

function AppTree(props) {
    return h('div', {class: 'row', id: props.id}, [
        h(
            'div',
            {class: 'col-lg-4 col-md-6 col-sm-12'},
            props.items.map(item => h(AppTreeNode, {item, key: item['@id'], machine: props.machine}))
        ),
        h(AppTreeForm, {
            class: 'col',
            families: props.families,
            fields: props.fields,
            id: `${props.id}-form`,
            machine: props.machine
        })
    ])
}

AppTree.props = {
    families: {required: true, type: Object},
    fields: {required: true, type: Object},
    id: {required: true, type: String},
    items: {required: true, type: Array},
    machine: {required: true, type: Object}
}

export default AppTree
