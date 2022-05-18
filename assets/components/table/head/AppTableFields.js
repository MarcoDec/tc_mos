import AppTableField from './AppTableField'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableFields(props) {
    const children = [h('th'), h('th', 'Actions')]
    for (const field of props.fields)
        children.push(h(AppTableField, {field, key: field.name}))
    return h('tr', children)
}

AppTableFields.props = {fields: generateTableFields()}

export default AppTableFields
