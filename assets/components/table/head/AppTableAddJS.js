import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../props'

function AppTableAddJS(props, context) {
    return h(
        resolveComponent('AppTableHeaderFormJS'),
        {
            fields: props.fields,
            icon: 'plus',
            id: props.id,
            label: 'Ajouter',
            machine: props.machine,
            mode: 'create',
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
        typeof context.slots['default'] === 'function'
            ? {submit: args => context.slots['default'](args)}
            : null
    )
}

AppTableAddJS.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableAddJS
