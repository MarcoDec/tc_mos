import AppTableItemField from './AppTableItemField'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableItem(props) {
    return h('tr', [
        h('td', {class: 'text-center'}, props.index + 1),
        h('td', {class: 'text-center'}),
        props.fields.map(field => h(AppTableItemField, {field, item: props.item, key: field.name}))
    ])
}

AppTableItem.props = {
    fields: generateTableFields(),
    index: {required: true, type: Number},
    item: {required: true, type: Object}
}

export default AppTableItem
