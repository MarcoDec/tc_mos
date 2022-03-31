import {computed, h} from 'vue'
import {useSlots} from '../../../composition/slots'

export default {
    props: {
        current: {required: false, type: Boolean}
    },
    setup(props, context) {
        const li = computed(() => ({
            active: props.current,
            'page-item': true
        }))
        const liProps = computed(() => (props.current ? {'aria-current': 'page', class: li.value} : {class: li.value}))
        return () => h('li', liProps.value, h('span', {class: 'page-link'}, useSlots(context)))
    }
}
