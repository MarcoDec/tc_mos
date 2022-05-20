import AppTableHeaders from './head/AppTableHeaders'
import AppTableItems from './body/AppTableItems'
import {generateTableFields} from '../validators'
import {h} from 'vue'

function AppTable(props, context) {
    function generateSlot(field, slots, type) {
        const slotName = `${type}(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            slots[slotName] = args => slot(args)
    }

    const cellSlots = {}
    const searchSlots = {}
    for (const field of props.fields) {
        generateSlot(field, cellSlots, 'cell')
        generateSlot(field, searchSlots, 'search')
    }
    return h(
        'div',
        {class: 'row', id: props.id},
        h('table', {class: 'col table table-bordered table-hover table-striped'}, [
            h(AppTableHeaders, {fields: props.fields, id: `${props.id}-headers`, store: props.store}, searchSlots),
            h(AppTableItems, {fields: props.fields, items: props.store.items}, cellSlots)
        ])
    )
}

AppTable.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    store: {required: true, type: Object}
}

export default AppTable
