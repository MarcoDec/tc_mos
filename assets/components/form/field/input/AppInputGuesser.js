import AppInput from './AppInput'
import {generateField} from '../../../validators'
import {h} from 'vue'

function AppInputGuesser(props) {
    return h(AppInput, props)
}

AppInputGuesser.props = {
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String}
}

export default AppInputGuesser
