import {computed, h, onMounted, onUnmounted, resolveComponent} from 'vue'
import {tableLoading, useTableMachine} from '../../machine'
import AppTable from '../../components/table/AppTable'
import generateItems from '../../stores/table/items'
import {generateTableFields} from '../../components/validators'
import {useRoute} from 'vue-router'

export default {
    props: {
        brands: {type: Boolean},
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
            if (typeof context.slots.create === 'function')
                children.create = args => context.slots.create(args)
            if (typeof context.slots.pagination === 'function')
                children.pagination = args => context.slots.pagination(args)
            if (typeof context.slots.search === 'function')
                children.search = args => context.slots.search(args)

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
                        h(resolveComponent('Fa'), {brands: props.brands, icon: props.icon}),
                        h('span', {class: 'ms-2'}, props.title)
                    ])),
                    h(AppTable, {fields: props.fields, id: `${route.name}-table`, machine, store}, children)
                ]
            )
        }
    }
}
