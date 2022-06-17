import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'
import {h} from 'vue'

function Fa(props) {
    return h(FontAwesomeIcon, {icon: props.brands ? ['fab', props.icon] : props.icon})
}

Fa.props = {brands: {type: Boolean}, icon: {required: true, type: String}}

export default Fa
