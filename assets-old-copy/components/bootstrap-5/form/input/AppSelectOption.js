import {h} from 'vue'

function AppSelectOption(props) {
    return h('option', {value: props.option.value}, props.option.text)
}

AppSelectOption.displayName = 'AppSelectOption'
AppSelectOption.props = {option: {required: true, type: Object}}

export default AppSelectOption
