import {h} from 'vue'

function AppOption(props) {
    return h('option', {value: props.option.value}, props.option.text)
}

AppOption.props = {option: {required: true, type: Object}}

export default AppOption
