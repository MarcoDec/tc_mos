import {h} from 'vue'
import {useSlots} from '../composition'

function AppOverlay(props, context) {
    const slots = useSlots(context)
    const content = props.loading
        ? [
            h(
                'div',
                {class: 'overlay'},
                h('div', {class: 'position-relative spinner-border start-50 top-50', role: 'status'})
            ),
            slots
        ]
        : slots
    return props.css === null ? content : h('div', {class: props.css}, content)
}

AppOverlay.displayName = 'AppOverlay'
AppOverlay.props = {css: {default: null, type: [Object, String]}, loading: {type: Boolean}}

export default AppOverlay
