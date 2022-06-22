import {generateTableField} from '../../../props'
import {h} from 'vue'

function AppTableSimpleField(props) {
    return h('th', null, props.field.label)
}

AppTableSimpleField.props = {field: generateTableField()}

export default AppTableSimpleField
