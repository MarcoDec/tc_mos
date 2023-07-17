import {generateField, generateLabelCols} from '../../props'
import {h} from 'vue'

function AppLabelJS(props) {
    return h('label', {class: `col-form-label ${props.cols}`, for: props['for']}, props.field.label)
}

AppLabelJS.props = {
    cols: generateLabelCols(),
    field: generateField(), for: {required: true, type: String}
}

export default AppLabelJS
