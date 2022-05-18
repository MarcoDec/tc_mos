import {generateTableField} from '../../validators'
import {h} from 'vue'

function AppTableField(props) {
    return h('th', props.field.label)
}

AppTableField.props = {field: generateTableField()}

export default AppTableField
