import {generateVariant} from './validators'
import {h} from 'vue'

function AppBtn(props, context) {
    return h(
        'button',
        {class: `btn btn-${props.variant}`, disabled: props.disabled, type: props.type},
        context.slots['default']()
    )
}

AppBtn.props = {
    disabled: {type: Boolean},
    type: {default: 'button', type: String},
    variant: generateVariant('primary')
}

export default AppBtn
