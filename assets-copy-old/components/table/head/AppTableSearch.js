import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../props'

function AppTableSearch(props, context) {
    const children = {
        default: () => h(resolveComponent('AppBtn'), {
            icon: 'times',
            async onClick() {
                props.machine.send('submit')
                await props.store.resetSearch()
                props.machine.send('success')
            },
            title: 'Annuler',
            variant: 'danger'
        })
    }
    if (typeof context.slots['default'] === 'function')
        children.submit = args => context.slots['default'](args)
    return h(
        resolveComponent('AppTableHeaderForm'),
        {
            fields: props.fields,
            icon: 'search',
            id: props.id,
            label: 'Rechercher',
            machine: props.machine,
            modelValue: props.store.search,
            onInputValue({field, value}) {
                props.store.search[field.name] = value
            },
            reverseIcon: 'plus',
            reverseLabel: 'ajout',
            reverseMode: 'create',
            store: props.store,
            async submit() {
                props.machine.send('submit')
                await props.store.fetch()
                props.machine.send('success')
            },
            type: 'search'
        },
        children
    )
}

AppTableSearch.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableSearch
