import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppRow(props, context) {
    return h('div', {class: 'row'}, useSlots(context))
}
