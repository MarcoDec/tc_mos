import AppTableSimpleField from './AppTableSimpleField'
import AppTableSortableField from './AppTableSortableField'
import {h} from 'vue'

function AppTableField(props) {
    return props.field.sort ? h(AppTableSortableField, props) : h(AppTableSimpleField, {field: props.field})
}

AppTableField.props = {
    field: {required: true, type: Object},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableField
