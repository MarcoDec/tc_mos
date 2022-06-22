import {generateLabelCols} from '../../props'
import {h} from 'vue'

function AppLabel(props) {
    return h('label', {class: `col-form-label ${props.cols}`, for: props['for']}, props.field.label)
}

AppLabel.props = {cols: generateLabelCols(), field: {required: true, type: Object}, for: {required: true, type: String}}

export default AppLabel
