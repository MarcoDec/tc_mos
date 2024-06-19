import {h} from 'vue'

function AppCol(props, context) {
    return h(props.tag, {class: props.cols === null ? 'col' : `col-${props.cols}`}, typeof context.slots.default === 'function' ? context.slots.default() : null)
}

AppCol.displayName = 'AppCol'
AppCol.props = {cols: {default: null, type: Number}, tag: {default: 'div', type: String}}

export default AppCol
