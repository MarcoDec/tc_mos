import {generateField, generateLabelCols} from '../../props'
import {h} from 'vue'
import Fa from '../../Fa'

function AppLabelJS(props) {
    if (props.field.info) {
        //On ajoute une icone cirlce-question point d'intérrogation pour les labels qui ont une info
        const children = []
        children.push(props.field.label)
        children.push(h(Fa, {icon: 'circle-info', title: `${props.field.info}`, class: 'overable', style: 'color: #0dcaf0; margin-left: 5px;'}))
        return h('label', {
            class: `col-form-label ${props.cols}`,
            for: props.for
        }, children)
    }
    return h('label', {class: `col-form-label ${props.cols}`,
        for: props.for}, props.field.label)
}

AppLabelJS.props = {
    cols: generateLabelCols(),
    field: generateField(), for: {required: true, type: String}
}

export default AppLabelJS
