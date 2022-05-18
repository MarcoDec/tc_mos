import {h} from 'vue'

function AppTableItemField(props) {
    return h('td', props.item[props.field.name])
}

AppTableItemField.props = {field: {required: true, type: Object}, item: {required: true, type: Object}}

export default AppTableItemField
