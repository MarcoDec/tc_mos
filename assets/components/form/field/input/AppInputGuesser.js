import AppInput from './AppInput.vue'
import {h} from 'vue'

function AppInputGuesser(props, context) {
    return h(AppInput, {
        'onUpdate:modelValue': value => context.emit('update:modelValue', value),
        ...props
    })
}

AppInputGuesser.emit = ['update:modelValue']
AppInputGuesser.props = {
    disabled: {type: Boolean},
    field: {required: true, type: Object},
    modelValue: {default: null, type: String}
}

export default AppInputGuesser
