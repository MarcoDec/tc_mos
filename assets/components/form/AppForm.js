import {h, resolveComponent} from 'vue'
import AppFormGroup from './field/AppFormGroup'
import {fieldValidator} from '../validators'

function AppForm(props, context) {
    const groups = []
    for (const field of props.fields)
        groups.push(h(AppFormGroup, {
            disabled: props.disabled,
            field,
            form: props.id,
            key: field.name,
            modelValue: props.modelValue[field.name],
            'onUpdate:modelValue': value => context.emit('update:modelValue', {
                ...props.modelValue,
                [field.name]: value
            }),
            violation: props.violations.find(violation => violation.propertyPath === field.name)
        }))
    groups.push(h(
        'div',
        {class: 'row'},
        h(
            'div',
            {class: 'col d-inline-flex justify-content-end'},
            typeof context.slots['default'] === 'function'
                ? context.slots['default']({
                    disabled: props.disabled,
                    form: props.id,
                    submitLabel: props.submitLabel,
                    type: 'submit'
                })
                : h(
                    resolveComponent('AppBtn'),
                    {disabled: props.disabled, form: props.id, type: 'submit'},
                    () => props.submitLabel
                )
        )
    ))
    return h('form', {
        autocomplete: 'off',
        enctype: 'multipart/form-data',
        id: props.id,
        method: 'POST',
        novalidate: true,
        onSubmit(e) {
            e.preventDefault()
            const data = new FormData(e.target)
            for (const [key, value] of Object.entries(Object.fromEntries(data))) {
                if (typeof value === 'undefined' || value === null)
                    data['delete'](key)
                if (typeof value === 'string') {
                    data.set(key, value.trim())
                    if (!props.noIgnoreNull && data.get(key).length === 0)
                        data['delete'](key)
                }
            }
            context.emit('submit', data)
        }
    }, groups)
}

AppForm.emits = ['submit', 'update:modelValue']
AppForm.props = {
    disabled: {type: Boolean},
    fields: {
        required: true,
        type: Array,
        validator(value) {
            if (value.length === 0)
                return false
            for (const field of value)
                if (!fieldValidator(field))
                    return false
            return true
        }
    },
    id: {required: true, type: String},
    modelValue: {default: () => ({}), type: Object},
    noIgnoreNull: {type: Boolean},
    submitLabel: {default: null, type: String},
    violations: {default: () => [], type: Array}
}

export default AppForm
