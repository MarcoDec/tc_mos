import AppPagination from './pagination/AppPagination'
import AppTableHeaders from './head/AppTableHeaders'
import AppTableItems from './body/AppTableItems'
import {generateTableFields} from '../props'
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
    if (typeof context.slots.create === 'function')
        searchSlots.create = args => context.slots.create(args)
    if (typeof context.slots.search === 'function')
        searchSlots.search = args => context.slots.search(args)
    for (const field of props.fields) {
        generateSlot(field, cellSlots, 'cell')
        generateSlot(field, searchSlots, 'search')
    }
    return h(
        'div',
        {id: props.id},
        h(
            'div',
            {class: 'row'},
            h('table', {class: 'col table table-bordered table-hover table-striped'}, [
                h(
                    AppTableHeaders,
                    {fields: props.fields, id: `${props.id}-headers`, machine: props.machine, store: props.store},
                    searchSlots
                ),
                h(
                    AppTableItems,
                    {fields: props.fields, id: `${props.id}-items`, items: props.store.items, machine: props.machine},
                    cellSlots
                )
            ])
        ),
        h(
            'div',
            {class: 'row'},
            h(
                AppPagination,
                {
                    class: 'col d-inline-flex justify-content-end',
                    machine: props.machine,
                    store: props.store
                },
                typeof context.slots.pagination === 'function'
                    ? args => context.slots.pagination(args)
                    : null
            )
        )
    )
}

AppTable.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTable
