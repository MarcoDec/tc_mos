import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../props'

function AppTableItem(props, context) {
    const removeAttrs = {
        icon: 'trash',
        item: props.item,
        async onClick() {
            props.machine.send('submit')
            await props.item.remove()
            props.machine.send('success')
        },
        title: 'Supprimer',
        variant: 'danger'
    }
    const children = [h('td', {class: 'text-center'}, props.index + 1)]
    if (!props.readonly)
        children.push(h('td', {class: 'text-center'}, [
            h(resolveComponent('AppBtn'), {
                icon: 'pencil-alt',
                onClick: () => props.machine.send('update', {updated: props.item['@id']}),
                title: 'Modifier',
                variant: 'primary'
            }),
            typeof context.slots.remove === 'function'
                ? context.slots.remove(removeAttrs)
                : h(resolveComponent('AppBtn'), removeAttrs)
        ]))
    children.push(props.fields.map(field => {
        const slot = context.slots[`cell(${field.name})`]
        return h(
            resolveComponent('AppTableItemField'),
            {field, id: `${props.id}-${field.name}`, item: props.item, key: field.name, machine: props.machine},
            typeof slot === 'function' ? args => slot(args) : null
        )
    }))
    return h('tr', {id: props.id}, children)
}

AppTableItem.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    index: {required: true, type: Number},
    item: {required: true, type: Object},
    machine: {required: true, type: Object},
    readonly: {type: Boolean}
}

export default AppTableItem
