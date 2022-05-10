import AppInput from './AppInput'
import {generateField} from '../../../validators'
import {h} from 'vue'

function AppInputGuesser(props) {
    return h(AppInput, {disabled: props.disabled, field: props.field, form: props.form, id: props.id})
}

AppInputGuesser.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String}
}

export default AppInputGuesser
