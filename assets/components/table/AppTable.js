import AppTableHeaders from './head/AppTableHeaders'
import AppTableItems from './body/AppTableItems'
import {generateTableFields} from '../validators'
import {h} from 'vue'

function AppTable(props) {
    return h(
        'div',
        {class: 'row'},
        h('table', {class: 'col table table-bordered table-hover table-striped'}, [
            h(AppTableHeaders, {fields: props.fields}),
            h(AppTableItems, {fields: props.fields, items: props.items})
        ])
    )
}

AppTable.props = {fields: generateTableFields(), items: {required: true, type: Object}}

export default AppTable
