import {h, resolveComponent} from 'vue'
import AppTableItemField from './AppTableItemField'
import {generateTableFields} from '../../validators'

function AppTableItem(props, context) {
    return h('tr', {id: props.id}, [
        h('td', {class: 'text-center'}, props.index + 1),
        h('td', {class: 'text-center'}, [
            h(resolveComponent('AppBtn'), {
                icon: 'pencil-alt',
                onClick: () => props.machine.send('update', {updated: props.item['@id']}),
                title: 'Modifier',
                variant: 'primary'
            })
        ]),
        props.fields.map(field => {
            const slot = context.slots[`cell(${field.name})`]
            return h(
                AppTableItemField,
                {field, id: `${props.id}-${field.name}`, item: props.item, key: field.name},
                typeof slot === 'function' ? args => slot(args) : null
            )
        })
    ])
}

AppTableItem.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    index: {required: true, type: Number},
    item: {required: true, type: Object},
    machine: {required: true, type: Object}
}

export default AppTableItem
