import {h} from 'vue'
import {useSlots} from '../../composition'

function AppCard(props, context) {
    return h('div', {class: 'card'}, [
        h(props.titleTag, {class: 'card-title'}, props.title),
        h('div', {class: 'card-body'}, useSlots(context))
    ])
}

AppCard.displayName = 'AppCard'
AppCard.props = {title: {required: true, type: String}, titleTag: {default: 'h2', type: String}}
 
export default AppCard
