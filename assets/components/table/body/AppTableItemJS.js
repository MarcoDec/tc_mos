import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../props'

function AppTableItemJS(props, context) {
    return h('tr', {id: props.id}, [
        h('td', {class: 'text-center'}, props.index + 1),
        h('td', {class: 'text-center'}, [
            h(resolveComponent('AppBtnJS'), {
                icon: 'pencil-alt',
                label: 'Modifier',
                onClick: () => props.machine.send('update', {updated: props.item['@id']}),
                title: 'Modifier',
                variant: 'primary'
            }),
            h(resolveComponent('AppBtnJS'), {
                icon: 'trash',
                label: 'supprimer',
                async onClick() {
                    props.machine.send('submit')
                    await props.item.remove()
                    props.machine.send('success')
                    const deletionEvent = new Event('deletion')
                    document.dispatchEvent(deletionEvent)
                },
                title: 'Supprimer',
                variant: 'danger'
            })
        ]),
        props.fields.map(field => {
            const slot = context.slots[`cell(${field.name})`]
            return h(
                resolveComponent('AppTableItemField'),
                {field, id: `${props.id}-${field.name}`, item: props.item, key: field.name, machine: props.machine, row: props.id},
                typeof slot === 'function' ? args => slot(args) : null
            )
        })
    ])
}

AppTableItemJS.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    index: {required: true, type: Number},
    item: {required: true, type: Object},
    machine: {required: true, type: Object},
    options: {default: {delete: true, modify: true, show: false}, required: false, type: Object}
}

export default AppTableItemJS
