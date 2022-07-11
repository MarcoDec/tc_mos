import AppTableAdd from './AppTableAdd'
import AppTableFields from './fields/AppTableFields'
import AppTableSearch from './AppTableSearch'
import {generateTableFields} from '../../props'
import {h} from 'vue'

function AppTableHeaders(props, context) {
    const rows = [h(AppTableFields, {fields: props.fields, machine: props.machine, readonly: props.readonly, store: props.store})]
    if (!props.readonly) {
        function generateFormRow(tag, type) {
            const children = {}
            if (type === 'form' && typeof context.slots.create === 'function')
                children['default'] = args => context.slots.create(args)
            if (type === 'search' && typeof context.slots.search === 'function')
                children['default'] = args => context.slots.search(args)

            function generateSlot(field) {
                const slotName = `${type}(${field.name})`
                const slot = context.slots[slotName]
                if (typeof slot === 'function')
                    children[slotName] = args => slot(args)
            }

            props.fields.forEach(generateSlot)
            return h(
                tag,
                {fields: props.fields, id: `${props.id}-${type}`, machine: props.machine, store: props.store},
                children
            )
        }

        rows.push(
            props.machine.state.value.matches('create')
                ? generateFormRow(AppTableAdd, 'form')
                : generateFormRow(AppTableSearch, 'search')
        )
    }
    return h('thead', {class: 'table-dark', id: props.id}, rows)
}

AppTableHeaders.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    readonly: {type: Boolean},
    store: {required: true, type: Object}
}

export default AppTableHeaders
