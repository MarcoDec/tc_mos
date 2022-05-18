import AppTableHeaders from './head/AppTableHeaders'
import {generateTableFields} from '../validators'
import {h} from 'vue'

function AppTable(props) {
    return h(
        'div',
        {class: 'row'},
        h(
            'table',
            {class: 'col table table-bordered table-hover table-striped'},
            h(AppTableHeaders, {fields: props.fields})
        )
    )
}

AppTable.props = {fields: generateTableFields()}

export default AppTable
