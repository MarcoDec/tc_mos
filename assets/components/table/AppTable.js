import AppTableHeaders from './head/AppTableHeaders'
import AppTableItems from './body/AppTableItems'
import {generateTableFields} from '../validators'
import {h} from 'vue'

function AppTable(props, context) {
    const children = {}
    for (const field of props.fields) {
        const slotName = `cell(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            children[slotName] = args => slot(args)
    }
    return h(
        'div',
        {class: 'row'},
        h('table', {class: 'col table table-bordered table-hover table-striped'}, [
            h(AppTableHeaders, {fields: props.fields}),
            h(AppTableItems, {fields: props.fields, items: props.items}, children)
        ])
    )
}

AppTable.props = {fields: generateTableFields(), items: {required: true, type: Object}}

export default AppTable
