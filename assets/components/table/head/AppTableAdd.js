import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../validators'

function AppTableAdd(props) {
    return h(resolveComponent('AppTableHeaderForm'), {
        fields: props.fields,
        icon: 'plus',
        id: props.id,
        label: 'Ajouter',
        machine: props.machine,
        async onSubmit(data) {
            props.machine.send('submit')
            try {
                await props.store.create(data)
                props.machine.send('success')
            } catch (violations) {
                props.machine.send('fail', {violations})
            }
        },
        reverseIcon: 'search',
        reverseLabel: 'recherche',
        reverseMode: 'search',
        store: props.store,
        submitVariant: 'success',
        type: 'form',
        variant: 'success',
        violations: props.machine.state.value.context.violations
    })
}

AppTableAdd.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableAdd
