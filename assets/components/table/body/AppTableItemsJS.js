//import AppTableItemJS from './AppTableItemJS'
import AppTableItemJSinVue from './AppTableItemJSinVue.vue'
import AppTableItemUpdateJS from './update/AppTableItemUpdateJS'
import {generateTableFields} from '../../props'
import {h} from 'vue'

function AppTableItemsJS(props, context) {
    const children = {}

    function generateSlot(field) {
        const slotName = `cell(${field.name})`
        const slot = context.slots[slotName]
        if (typeof slot === 'function')
            children[slotName] = args => slot(args)
    }

    props.fields.forEach(generateSlot)
    return h('tbody', {id: props.id}, props.items.map((item, index) => h(
        props.machine.state.value.matches('update') && props.machine.state.value.context.updated === item['@id']
            ? AppTableItemUpdateJS
            : AppTableItemJSinVue,
        {fields: props.fields, id: `${props.id}-${item.id}`, index, item, key: item['@id'], machine: props.machine, options: props.options},
        children
    )))
}

AppTableItemsJS.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    items: {required: true, type: Object},
    machine: {required: true, type: Object},
    options: {default: () => ({delete: true, modify: true, show: false}), required: false, type: Object}
}

export default AppTableItemsJS
