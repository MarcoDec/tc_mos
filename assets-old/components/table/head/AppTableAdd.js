import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../props'

function AppTableAdd(props, context) {
    const children = {}
    let hasChildren = false
    if (typeof context.slots['default'] === 'function') {
        children.submit = args => context.slots['default'](args)
        hasChildren = true
    }
    for (const field of props.fields)
        if (!field.create) {
            children[`form(${field.name})`] = () => h('td')
            hasChildren = true
        }
    return h(
        resolveComponent('AppTableHeaderForm'),
        {
            fields: props.fields,
            icon: 'plus',
            id: props.id,
            label: 'Ajouter',
            machine: props.machine,
            reverseIcon: 'search',
            reverseLabel: 'recherche',
            reverseMode: 'search',
            store: props.store,
            async submit(data) {
                props.machine.send('submit')
                try {
                    await props.store.create(props.fields, data)
                    props.machine.send('success')
                } catch (violations) {
                    props.machine.send('fail', {violations})
                }
            },
            submitVariant: 'success',
            type: 'form',
            variant: 'success',
            violations: props.machine.state.value.context.violations
        },
        hasChildren ? children : null
    )
}

AppTableAdd.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableAdd
