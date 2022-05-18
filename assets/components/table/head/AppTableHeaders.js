import AppTableFields from './AppTableFields'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableHeaders(props) {
    return h('thead', {class: 'table-dark'}, h(AppTableFields, {fields: props.fields}))
}

AppTableHeaders.props = {fields: generateTableFields()}

export default AppTableHeaders
