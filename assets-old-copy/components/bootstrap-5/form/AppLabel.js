import {h, resolveComponent} from 'vue'
import {useSlots} from '../../../composition'

function AppLabel(props, context) {
    return h(
        resolveComponent('AppCol'),
        {...props, class: 'form-label', tag: 'label'},
        () => useSlots(context)
    )
}

AppLabel.displayName = 'AppLabel'
AppLabel.props = {cols: {default: 2, type: Number}}

export default AppLabel
