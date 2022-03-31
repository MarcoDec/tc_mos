import {h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default function AppInvalidFeedback(props, context) {
    return h('div', {class: 'invalid-feedback'}, useSlots(context))
}
