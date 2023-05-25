import {computed, h, resolveComponent} from 'vue'
import {generateTableField} from '../../../props'

export default {
    props: {
        field: generateTableField(),
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    },
    setup(props) {
        console.log('props.store', props.store)
        const ariaSort = computed(() => (props.store.ariaSort ? props.store.ariaSort(props.field) : null))
        const sorted = computed(() => (props.store.isSorter ? props.store.isSorter(props.field) : null))
        const down = computed(() => ({'text-secondary': !sorted.value || props.store.asc}))
        const up = computed(() => ({'text-secondary': !sorted.value || !props.store.asc}))

        async function onClick() {
            props.machine.send('search')
            props.machine.send('submit')
            await props.store.sort(props.field)
            props.machine.send('success')
        }

        return () => h(
            'th',
            {ariaSort: ariaSort.value, onClick},
            h('span', {class: 'justify-content-between'}, [
                h('span', props.field.label),
                h('span', {class: 'd-flex flex-column'}, [
                    h(resolveComponent('Fa'), {class: up.value, icon: 'caret-up'}),
                    h(resolveComponent('Fa'), {class: down.value, icon: 'caret-down'})
                ])
            ])
        )
    }
}
