import {h, resolveComponent} from 'vue'
import {generateTableFields} from '../../validators'

function AppTableAdd(props) {
    return h(resolveComponent('AppTableHeaderForm'), {
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
            await props.store.create(data)
        },
        submitVariant: 'success',
        type: 'form',
        variant: 'success'
    })
}

AppTableAdd.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    machine: {required: true, type: Object},
    store: {required: true, type: Object}
}

export default AppTableAdd
