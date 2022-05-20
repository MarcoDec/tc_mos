import {h, resolveComponent} from 'vue'
import AppTableSearchField from './AppTableSearchField'
import {generateTableFields} from '../../../validators'

function AppTableSearch(props, context) {
    const formId = `${props.id}-form`
    const children = [
        h('td', h(resolveComponent('Fa'), {icon: 'filter'})),
        h('td', [
            h(
                resolveComponent('AppForm'),
                {
                    fields: props.fields,
                    id: formId,
                    inline: true,
                    noContent: true,
                    onSubmit: async () => {
                        await props.store.fetch()
                    },
                    submitLabel: 'Rechercher'
                },
                ({disabled, form, submitLabel, type}) => h(resolveComponent('AppBtn'), {
                    disabled,
                    form,
                    icon: 'search',
                    title: submitLabel,
                    type,
                    variant: 'secondary'
                })
            ),
            h(resolveComponent('AppBtn'), {
                icon: 'times',
                async onClick() {
                    await props.store.resetSearch()
                },
                title: 'Annuler',
                variant: 'danger'
            })
        ])
    ]

    function generateField(field) {
        const slot = context.slots[`search(${field.name})`]
        children.push(h(AppTableSearchField, {
            field,
            form: formId,
            id: `${props.id}-${field.name}`,
            modelValue: props.store.search[field.name],
            'onUpdate:modelValue': value => {
                props.store.search[field.name] = value
            }
        }, typeof slot === 'function' ? args => slot(args) : null))
    }

    props.fields.forEach(generateField)
    return h('tr', {class: 'text-center', id: props.id}, children)
}

AppTableSearch.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    store: {required: true, type: Object}
}

export default AppTableSearch
