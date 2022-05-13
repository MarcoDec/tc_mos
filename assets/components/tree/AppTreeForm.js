import {h, resolveComponent} from 'vue'

const fields = [
    {label: 'Parent', name: 'parent'},
    {label: 'Code', name: 'code'},
    {label: 'Nom', name: 'name'},
    {label: 'Cuivre', name: 'copperable'},
    {label: 'Code douanier', name: 'customsCode'},
    {label: 'Icône', name: 'file', type: 'file'}
]

function AppTreeForm(props) {
    return h(
        resolveComponent('AppCard'),
        {id: props.id, title: 'Ajouter une famille'},
        () => h(resolveComponent('AppForm'), {fields, id: `${props.id}-create`})
    )
}

AppTreeForm.props = {id: {required: true, type: String}}

export default AppTreeForm
