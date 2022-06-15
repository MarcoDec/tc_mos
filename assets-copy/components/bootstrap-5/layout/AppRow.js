import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppRow(props, context) {
    return h('div', {...props, class: 'row'}, useSlots(context))
}

AppRow.displayName = 'AppRow'

export default AppRow
