import {generateField} from '../../../props'
import {h} from 'vue'

function AppInputFile(props, context) {
    return h('input', {
        class: 'form-control form-control-sm',
        disabled: props.disabled,
        form: props.form,
        id: props.id,
        name: props.field.name,
        onInput: e => context.emit('update:modelValue', URL.createObjectURL(e.target.files[0])),
        type: props.field.type ?? 'text'
    })
}

AppInputFile.emits = ['update:modelValue']
AppInputFile.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: false, type: String},
    id: {required: false, type: String}
}

export default AppInputFile
