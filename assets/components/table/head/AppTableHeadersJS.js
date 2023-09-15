import AppTableAddJS from './AppTableAddJS'
import AppTableFieldsJS from './field/AppTableFieldsJS'
import AppTableSearchJS from './AppTableSearchJS'
import {generateTableFields} from '../../props'
import {h} from 'vue'

function AppTableHeadersJS(props, context) {
    function generateFormRow(tag, type) {
        const children = {}
        if (type === 'form' && typeof context.slots.create === 'function')
            children.default = args => context.slots.create(args)
        if (type === 'search' && typeof context.slots.search === 'function')
            children.default = args => context.slots.search(args)

        function generateSlot(field) {
            const slotName = `${type}(${field.name})`
            const slot = context.slots[slotName]
            if (typeof slot === 'function')
                children[slotName] = args => slot(args)
        }

        props.fields.forEach(generateSlot)
        return h(
            tag,
            {btnbasculesearch: props.btnbasculesearch, fields: props.fields, id: `${props.id}-${type}`, machine: props.machine, store: props.store},
            children
        )
    }

    return h('thead', {class: 'table-dark', id: props.id}, [
        h(AppTableFieldsJS, {fields: props.fields, machine: props.machine, store: props.store}),
        props.machine.state.value.matches('create')
            ? generateFormRow(AppTableAddJS, 'form')
            : generateFormRow(AppTableSearchJS, 'search')
    ])
}

AppTableHeadersJS.props = {
    btnbasculesearch: {default: false, type: Boolean},
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableHeadersJS
