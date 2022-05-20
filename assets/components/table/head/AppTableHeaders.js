import AppTableFields from './AppTableFields'
import AppTableSearch from './search/AppTableSearch'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableHeaders(props, context) {
    const children = {}

    function generateSlot(field) {
        const slotName = `search(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            children[slotName] = args => slot(args)
    }

    props.fields.forEach(generateSlot)
    return h('thead', {class: 'table-dark', id: props.id}, [
        h(AppTableFields, {fields: props.fields, machine: props.machine, store: props.store}),
        h(
            AppTableSearch,
            {fields: props.fields, id: `${props.id}-search`, machine: props.machine, store: props.store},
            children
        )
    ])
}

AppTableHeaders.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableHeaders
