import AppTreeForm from './AppTreeForm'
import AppTreeNode from './AppTreeNode'
import {generateFields} from '../props'
import {h} from 'vue'

function AppTree(props) {
    return h('div', {class: 'row', id: props.id}, [
        h(
            'div',
            {class: 'col-lg-3 col-md-6 col-sm-12'},
            props.items.map(item => h(AppTreeNode, {item, key: item['@id'], machine: props.machine}))
        ),
        h(AppTreeForm, {
            attributes: props.attributes,
            class: 'col',
            families: props.families,
            fields: props.fields,
            id: `${props.id}-form`,
            machine: props.machine,
            noDisplayAttr: props.noDisplayAttr
        })
    ])
}

AppTree.props = {
    attributes: {required: true, type: Object},
    families: {required: true, type: Object},
    fields: generateFields(),
    id: {required: true, type: String},
    items: {required: true, type: Array},
    machine: {required: true, type: Object},
    noDisplayAttr: {type: Boolean}
}

export default AppTree
