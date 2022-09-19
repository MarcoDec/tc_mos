import AppTableItem from './AppTableItem'
import AppTableItemUpdate from './AppTableItemUpdate'
import {generateTableFields} from '../../props'
import {h} from 'vue'

function AppTableItems(props, context) {
    const children = {}
    if (typeof context.slots.remove === 'function')
        children.remove = args => context.slots.remove(args)

    function generateSlot(field) {
        const slotName = `cell(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            children[slotName] = args => slot(args)
    }

    props.fields.forEach(generateSlot)
    return h('tbody', {id: props.id}, props.items.map((item, index) => h(
        props.machine.state.value.matches('update') && props.machine.state.value.context.updated === item['@id']
            ? AppTableItemUpdate
            : AppTableItem,
        {fields: props.fields, id: `${props.id}-${item.id}`, index, item, key: item['@id'], machine: props.machine, readonly: props.readonly},
        children
    )))
}

AppTableItems.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    items: {required: true, type: Object},
    machine: {required: true, type: Object},
    readonly: {type: Boolean}
}

export default AppTableItems
