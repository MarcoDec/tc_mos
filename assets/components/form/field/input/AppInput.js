import {generateField} from '../../../validators'
import {h} from 'vue'

function AppInput(props) {
    return h('input', {
        autocomplete: 'off',
        class: 'form-control form-control-sm',
        disabled: props.disabled,
        form: props.form,
        id: props.id,
        name: props.field.name,
        placeholder: props.field.label,
        type: props.field.type ?? 'text'
    })
}

AppInput.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String}
}

export default AppInput
