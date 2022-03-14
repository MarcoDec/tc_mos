import {computed, h} from 'vue'
import type {FunContext} from '../../../types/vue'
import type {VNode} from 'vue'
import {useSlots} from '../../../composition/slots'

type Props = {
    current: boolean
}

export default {
    props: {
        current: {required: false, type: Boolean}
    },
    setup(props: Readonly<Props>, context: FunContext): () => VNode {
        const li = computed(() => ({
            active: props.current,
            'page-item': true
        }))
        const liProps = computed(() => (props.current ? {'aria-current': 'page', class: li.value} : {class: li.value}))
        return (): VNode => h('li', liProps.value, h('span', {class: 'page-link'}, useSlots(context)))
    }
}
