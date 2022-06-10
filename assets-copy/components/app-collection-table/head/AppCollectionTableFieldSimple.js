import {h} from 'vue'

function AppCollectionTableFieldSimple(props) {
    return h('th', props.field.label)
}

AppCollectionTableFieldSimple.displayName = 'AppCollectionTableFieldSimple'
AppCollectionTableFieldSimple.props = {field: {required: true, type: Object}}

export default AppCollectionTableFieldSimple
