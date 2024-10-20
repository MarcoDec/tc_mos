import {fieldValidator, generateLabelCols} from '../props'
import {h, resolveComponent} from 'vue'
import AppFormField from './field/AppFormField.vue'

function AppFormJS(props, context) {
    function generateSlot() {
        return context.slots.default({
            disabled: props.disabled,
            form: props.id,
            submitLabel: props.submitLabel,
            type: 'submit'
        })
    }

    const groups = []
    if (props.noContent) {
        if (typeof context.slots.default === 'function')
            groups.push(generateSlot())
    } else {
        for (const field of props.fields) {
            groups.push(h(AppFormField, {
                disabled: props.disabled,
                field,
                form: props.id,
                key: field.name,
                labelCols: props.labelCols,
                modelValue: props.modelValue[field.name],
                'onUpdate:modelValue': value => context.emit('update:modelValue', {
                    ...props.modelValue,
                    [field.name]: value
                }),
                violation: props.violations.find(violation => violation.propertyPath === field.name)
            }))
        }

        if (props.submitLabel !== null){
            groups.push(h(
                'div',
                {class: 'row'},
                h(
                    'div',
                    {class: 'col d-inline-flex justify-content-end'},
                    typeof context.slots.default === 'function'
                        ? generateSlot()
                        : h(
                            resolveComponent('AppBtnJS'),
                            {
                                disabled: props.disabled,
                                form: props.id,
                                type: 'submit',
                                onClick: e => {
                                    e.preventDefault()
                                    context.emit('submit', e)
                                }
                            },
                            () => props.submitLabel
                        )
                )
            ))
        }
    }
    const attrs = {
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
                    data.delete(key)
                if (typeof value === 'string') {
                    data.set(key, value.trim())
                    if (!props.noIgnoreNull && data.get(key).length === 0)
                        data.delete(key)
                }
            }
        }
    }
    if (props.inline)
        attrs.class = 'd-inline m-0 p-0'
    return h('form', attrs, groups)
}

AppFormJS.emits = ['submit', 'update:modelValue']
AppFormJS.props = {
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
    inline: {type: Boolean},
    labelCols: generateLabelCols(),
    modelValue: {default: () => ({}), type: Object},
    noContent: {type: Boolean},
    noIgnoreNull: {type: Boolean},
    submitLabel: {default: null, type: String},
    violations: {default: () => [], type: Array}
}

export default AppFormJS
