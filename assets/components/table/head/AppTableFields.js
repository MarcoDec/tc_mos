import AppTableField from './AppTableField'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableFields(props) {
    const children = [h('th'), h('th', 'Actions')]
    for (const field of props.fields)
        children.push(h(AppTableField, {field, key: field.name, machine: props.machine, store: props.store}))
    return h('tr', children)
}

AppTableFields.props = {
    fields: generateTableFields(),
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableFields
