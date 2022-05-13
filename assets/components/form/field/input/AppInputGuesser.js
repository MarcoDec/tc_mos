import AppInput from './AppInput'
import AppSelect from './select/AppSelect'
import {generateField} from '../../../validators'
import {h} from 'vue'

function getType(field) {
    switch (field.type) {
    case 'select':
        return AppSelect
    default:
        return AppInput
    }
}

function AppInputGuesser(props) {
    return h(getType(props.field), {disabled: props.disabled, field: props.field, form: props.form, id: props.id})
}

AppInputGuesser.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String}
}

export default AppInputGuesser
