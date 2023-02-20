import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppCol(props, context) {
    return h(props.tag, {class: props.cols === null ? 'col' : `col-${props.cols}`}, useSlots(context))
}

AppCol.displayName = 'AppCol'
AppCol.props = {cols: {default: null, type: Number}, tag: {default: 'div', type: String}}

export default AppCol
