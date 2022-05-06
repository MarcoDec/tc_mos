import {h} from 'vue'
import {useSlots} from '../../../composition'

function AppInvalidFeedback(props, context) {
    return h('div', {class: 'invalid-feedback'}, useSlots(context))
}

AppInvalidFeedback.displayName = 'AppInvalidFeedback'

export default AppInvalidFeedback
