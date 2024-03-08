import {generateField, generateLabelCols} from '../../props'
import {h} from 'vue'

function AppLabel(props) {
    return h('label', {class: `col-form-label ${props.cols}`, for: props.for}, props.field.label)
}

AppLabel.props = {
    cols: generateLabelCols(),
    field: generateField(), for: {required: true, type: String}
}

export default AppLabel
