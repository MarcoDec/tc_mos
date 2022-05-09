import {generateField} from '../../validators'
import {h} from 'vue'

function AppLabel(props) {
    return h('label', {class: 'col-form-label col-md-3 col-xs-12', for: props['for']}, props.field.label)
}

AppLabel.props = {field: generateField(), for: {required: true, type: String}}

export default AppLabel
