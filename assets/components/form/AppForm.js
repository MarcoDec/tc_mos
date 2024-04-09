import {computed, h, resolveComponent} from 'vue'
import {fieldValidator, generateLabelCols} from '../props'
import AppFormField from './field/AppFormField.vue'
import AppTabs from '../tab/AppTabs.vue'

export default {
    props: {
        countryField: {default: null, type: String},
        disabled: {type: Boolean},
        fields: {
            required: true,
            type: Array,
            validator(value) {
                if (value.length === 0) return false
                for (const field of value) if (!fieldValidator(field)) return false
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
    },
    setup(props, context) {
        const tabs = computed(() => {
            for (const field of props.fields) if (field.mode === 'tab') return true
            return false
        })

        return () => {
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
                if (tabs.value) {
                    groups.push(
                        h(AppTabs, () =>
                            props.fields.map(field =>
                                h(AppFormField, {
                                    countryField:
                    props.countryField === null
                        ? null
                        : props.modelValue[props.countryField],
                                    disabled: props.disabled,
                                    field,
                                    form: props.id,
                                    key: field.name,
                                    labelCols: props.labelCols,
                                    modelValue: props.modelValue,
                                    'onUpdate:modelValue': value =>
                                        context.emit('update:modelValue', {
                                            ...props.modelValue,
                                            [field.name]: value
                                        }),
                                    violation: props.violations.find(
                                        violation => violation.propertyPath === field.name
                                    )
                                })))
                    )
                } else {
                    for (const field of props.fields)
                        groups.push(
                            h(AppFormField, {
                                disabled: props.disabled,
                                field,
                                form: props.id,
                                key: field.name,
                                labelCols: props.labelCols,
                                modelValue: props.modelValue,
                                'onUpdate:modelValue': value =>
                                    context.emit('update:modelValue', {
                                        ...props.modelValue,
                                        [field.name]: value
                                    }),
                                violation: props.violations.find(
                                    violation => violation.propertyPath === field.name
                                )
                            })
                        )
                }

                if (props.submitLabel !== null) {
                    groups.push(
                        h(
                            'div',
                            {class: 'row'},
                            h(
                                'div',
                                {class: 'col d-inline-flex justify-content-end'},
                                typeof context.slots.default === 'function'
                                    ? generateSlot()
                                    : h(
                                        resolveComponent('AppBtn'),
                                        {
                                            disabled: props.disabled,
                                            form: props.id,
                                            type: 'submit'
                                        },
                                        () => props.submitLabel
                                    )
                            )
                        )
                    )
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
                    context.emit('submit', data)
                }
            }
            if (props.inline) attrs.class = 'd-inline m-0 p-0'
            return h('form', attrs, groups)
        }
    }
}
