import AppTableItem from './AppTableItem'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableItems(props) {
    return h(
        'tbody',
        props.items.map((item, index) => h(AppTableItem, {fields: props.fields, index, item, key: item['@id']}))
    )
}

AppTableItems.props = {fields: generateTableFields(), items: {required: true, type: Object}}

export default AppTableItems
