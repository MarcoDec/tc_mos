import {h, resolveComponent} from 'vue'
import {generateField} from '../../../props'

function AppInputNumber(props, context) {
    return h(resolveComponent('AppInput'), {
        disabled: props.disabled,
        field: {...props.field, type: 'text'},
        form: props.form,
        id: props.id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => {
            const parsed = parseFloat(value)
            context.emit('update:modelValue', isNaN(parsed) ? 0 : parsed)
        }
    })
}

AppInputNumber.emits = ['update:modelValue']
AppInputNumber.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true},
    id: {required: true},
    modelValue: {}
}

export default AppInputNumber
