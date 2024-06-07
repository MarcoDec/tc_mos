import {fieldValidator, generateLabelCols} from '../props'
import {h, ref, resolveComponent} from 'vue'
import AppFormField from './field/AppFormField.vue'

const focusedField = ref(null)
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
        //  for (const field of props.fields)
        //     groups.push(h(AppFormField, {
        //         disabled: props.disabled,
        //         field,
        //         form: props.id,
        //         key: field.name,
        //         labelCols: props.labelCols,
        //         modelValue: props.modelValue[field.name],
        //         'onUpdate:modelValue': value => context.emit('update:modelValue', {
        //             ...props.modelValue,
        //             [field.name]: value
        //             // [field.children[1].name]: value
        //         }),
        //         violation: props.violations.find(violation => violation.propertyPath === field.name)
        //     }))
        for (const field of props.fields) {
            // if (field.childrens) {
            //     console.log("has children", field)
            //     for (const child of field.children) {
            //         groups.push(h(AppFormField, {
            //             disabled: props.disabled,
            //             field: child,
            //             form: props.id,
            //             key: child.name,
            //             labelCols: props.labelCols,
            //             modelValue: props.modelValue[child.name],
            //             newField: field,
            //             'onUpdate:modelValue': value => context.emit('update:modelValue', {
            //                 ...props.modelValue,
            //                 [child.name]: value
            //             }),
            //             violation: props.violations.find(violation => violation.propertyPath === child.name)
            //         }))
            //     }
            // } else {
            //     console.log("no children", field)
            groups.push(h(AppFormField, {
                disabled: props.disabled,
                field,
                focusedField,
                form: props.id,
                key: field.name,
                labelCols: props.labelCols,
                modelValue: props.modelValue[field.name],
                'onUpdate:modelValue': value => context.emit('update:modelValue', {
                    ...props.modelValue,
                    [field.name]: value
                }),
                'onFocusin': value => focusedField.value = value,
                violation: props.violations.find(violation => violation.propertyPath === field.name)
            }))
            // }
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
                                onClick: () => {
                                    console.log("onClick Button submit")
                                    //context.emit('submit')
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
        'onSubmit.prevent': () => {},
        onSubmit(e) {
            console.log("onSubmit")
            e.preventDefault()
            const data = new FormData(e.target)
            // console.log('data before', data)
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
