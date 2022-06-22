import {h} from 'vue'

function AppTableSimpleField(props) {
    return h('th', null, props.field.label)
}

AppTableSimpleField.props = {field: {required: true, type: Object}}

export default AppTableSimpleField
