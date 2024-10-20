import {generateTableFields, generateVariant} from '../../props'
import {h, resolveComponent} from 'vue'

function AppTableHeaderFormJS(props, context) {
    const formId = `${props.id}-form`
    const formTd = [
        typeof context.slots.submit === 'function'
            ? context.slots.submit({
                fields: props.fields,
                icon: props.icon,
                id: formId,
                inline: true,
                machine: props.machine,
                noContent: true,
                store: props.store,
                submitLabel: props.label,
                variant: props.submitVariant
            })
            : h(
                resolveComponent('AppFormJS'),
                {
                    fields: props.fields,
                    id: formId,
                    inline: true,
                    noContent: true,
                    async onSubmit(data) {
                        props.machine.send('submit')
                        await props.submit(data)
                        props.machine.send('success')
                    },
                    submitLabel: props.label
                },
                ({disabled, form, submitLabel, type}) => h(resolveComponent('AppBtnJS'), {
                    disabled,
                    form,
                    icon: props.icon,
                    label: 'submitLabel',
                    title: submitLabel,
                    type,
                    variant: props.submitVariant
                })
            )
    ]
    if (typeof context.slots.default === 'function')
        formTd.push(context.slots.default())
    const children = props.btnbasculesearch ? [
        h('td', [
            h(resolveComponent('Fa'), {icon: props.icon})
        ]),
        h('td', formTd)
    ] : [
        h('td', [
            h(resolveComponent('Fa'), {icon: props.icon}),
            h(resolveComponent('AppBtnJS'), {
                icon: props.reverseIcon,
                label: `Basculer en mode ${props.reverseLabel}`,
                onClick: () => props.machine.send(props.reverseMode),
                title: `Basculer en mode ${props.reverseLabel}`,
                variant: 'primary'
            })
        ]),
        h('td', formTd)
    ]
    function generateField(field) {
        const slot = context.slots[`${props.type}(${field.name})`]
        children.push(h(resolveComponent('AppTableFormField'), {
            field,
            form: formId,
            id: `${props.id}-${field.name}`,
            machine: props.machine,
            modelValue: props.modelValue[field.name],
            'onUpdate:modelValue': value => context.emit('inputValue', {field, value}),
            store: props.store,
            violation: props.violations.find(violation => violation.propertyPath === field.name)
        }, typeof slot === 'function' ? args => slot(args) : null))
    }

    props.fields.forEach(generateField)
    return h('tr', {class: `table-${props.variant} text-center`, id: props.id}, children)
}

AppTableHeaderFormJS.props = {
    btnbasculesearch: {default: false, type: Boolean},
    fields: generateTableFields(),
    icon: {required: true, type: String},
    id: {required: true, type: String},
    label: {required: true, type: String},
    machine: {required: true, type: Object},
    modelValue: {default: () => ({}), type: Object},
    reverseIcon: {required: true, type: String},
    reverseLabel: {required: true, type: String},
    reverseMode: {required: true, type: String},
    store: {required: true, type: Object},
    submit: {required: true, type: Function},
    submitVariant: generateVariant('secondary'),
    type: {required: true, type: String},
    variant: generateVariant('dark'),
    violations: {default: () => [], type: Array}
}

export default AppTableHeaderFormJS
