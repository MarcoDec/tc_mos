import {computed, h, onMounted, onUnmounted, resolveComponent} from 'vue'
import {tableLoading, useTableMachine} from '../../machine'
import AppTable from '../../components/table/AppTable'
import generateItems from '../../stores/table/items'
import {generateTableFields} from '../../components/validators'
import {useRoute} from 'vue-router'

export default {
    props: {
        fields: generateTableFields(),
        icon: {required: true, type: String},
        title: {required: true, type: String}
    },
    setup(props, context) {
        onMounted(async () => {
            await store.fetch()
            machine.send('success')
        })

        onUnmounted(() => {
            store.dispose()
        })

        const route = useRoute()
        const machine = useTableMachine(route.name)
        const store = generateItems(route.name)
        const variant = computed(() => `text-${store.length > 0 ? 'dark' : 'white'}`)

        machine.send('submit')

        return () => {
            const children = {}

            function generateSlot(field, type) {
                const slotName = `${type}(${field.name})`
                const slot = context.slots[slotName]
                if (typeof slot === 'function')
                    children[slotName] = args => slot(args)
            }

            for (const field of props.fields) {
                generateSlot(field, 'cell')
                generateSlot(field, 'search')
            }
            return h(
                resolveComponent('AppOverlay'),
                {class: variant.value, id: route.name, spinner: tableLoading.some(machine.state.value.matches)},
                () => [
                    h('div', {class: 'row'}, h('h1', {class: 'col'}, [
                        h(resolveComponent('Fa'), {icon: props.icon}),
                        h('span', {class: 'ms-2'}, props.title)
                    ])),
                    h(AppTable, {fields: props.fields, id: `${route.name}-table`, machine, store}, children)
                ]
            )
        }
    }
}
