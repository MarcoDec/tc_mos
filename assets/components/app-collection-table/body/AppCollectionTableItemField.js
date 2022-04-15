import {h} from 'vue'

function AppCollectionTableItemField(props) {
    return h('td', props.item[props.field.name])
}

AppCollectionTableItemField.displayName = 'AppCollectionTableItemField'
AppCollectionTableItemField.props = {field: {required: true, type: Object}, item: {required: true, type: Object}}

export default AppCollectionTableItemField
