import {h, resolveComponent} from 'vue'
import AppTableItemUpdateFieldJS from './AppTableItemUpdateFieldJS'
import {generateTableFields} from '../../../props'

function AppTableItemUpdateJS(props, context) {
    const formId = `${props.id}-update`
    return h('tr', {id: props.id}, [
        h('td', {class: 'text-center'}, props.index + 1),
        h('td', {class: 'text-center'}, [
            h(
                resolveComponent('AppFormJS'),
                {
                    fields: props.fields,
                    id: formId,
                    inline: true,
                    noContent: true,
                    async onSubmit(data) {
                        props.machine.send('submit')
                        try {
                            await props.item.update(props.fields, data)
                            props.machine.send('success')
                            props.machine.send('search')
                        } catch (violations) {
                            props.machine.send('fail', {violations})
                        }
                    },
                    submitLabel: 'Modifier'
                },
                ({disabled, form, submitLabel, type}) => h(resolveComponent('AppBtnJS'), {
                    disabled,
                    form,
                    icon: 'check',
                    title: submitLabel,
                    type,
                    variant: 'success'
                })
            ),
            h(resolveComponent('AppBtnJS'), {
                icon: 'times',
                onClick: () => props.machine.send('search'),
                title: 'Annuler',
                variant: 'danger'
            })
        ]),
        props.fields.map(field => {
            const slot = context.slots[`form(${field.name})`]
            return h(
                field.update ? AppTableItemUpdateFieldJS : resolveComponent('AppTableItemField'),
                {
                    field,
                    form: formId,
                    id: `${props.id}-field`,
                    item: props.item,
                    key: field.name,
                    machine: props.machine
                },
                typeof slot === 'function' ? args => slot(args) : null
            )
        })
    ])
}

AppTableItemUpdateJS.props = {
    fields: generateTableFields(),
    id: {required: true, type: String},
    index: {required: true, type: Number},
    item: {required: true, type: Object},
    machine: {required: true, type: Object}
}

export default AppTableItemUpdateJS
