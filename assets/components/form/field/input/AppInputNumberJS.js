import {h, resolveComponent} from 'vue'
import {generateField} from '../../../props'

function AppInputNumberJS(props, context) {
    return h(resolveComponent('AppInput'), {
        disabled: props.disabled,
        field: {...props.field, type: 'text'},
        form: props.form,
        id: props.id,
        modelValue: props.modelValue?.value,
        'onUpdate:modelValue': value => {
            const parsed = parseFloat(value)
            context.emit('update:modelValue', isNaN(parsed) ? 0 : parsed)
        }
    })
}

AppInputNumberJS.emits = ['update:modelValue']
AppInputNumberJS.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppInputNumberJS
