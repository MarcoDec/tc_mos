import AppTableItem from './AppTableItem'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableItems(props, context) {
    const children = {}
    for (const field of props.fields) {
        const slotName = `cell(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            children[slotName] = args => slot(args)
    }
    return h('tbody', props.items.map((item, index) => h(
        AppTableItem,
        {fields: props.fields, index, item, key: item['@id']},
        children
    )))
}

AppTableItems.props = {fields: generateTableFields(), items: {required: true, type: Object}}

export default AppTableItems
