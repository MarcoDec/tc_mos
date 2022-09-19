import AppInput from './AppInput.vue'
import {h} from 'vue'

function AppInputGuesser(props) {
    return h(AppInput, props)
}

AppInputGuesser.props = {field: {required: true, type: Object}}

export default AppInputGuesser
