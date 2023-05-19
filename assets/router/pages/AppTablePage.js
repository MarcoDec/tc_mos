import {computed, h, onMounted, onUnmounted, resolveComponent} from 'vue'
import {tableLoading, useTableMachine} from '../../machine'
import AppTable from '../../components/table/AppTable'
import generateItems from '../../stores/tables/items'
import {generateTableFields} from '../../components/props'
import {useRoute} from 'vue-router'

export default {
    props: {
        brands: {type: Boolean},
        fields: generateTableFields(),
        icon: {required: false, type: String},
        machine: {default: null, type: Object},
        store: {default: null, type: Object},
        title: {required: false, type: String}
    },
    setup(props, context) {
        const route = useRoute()
        const machines = props.machine ?? useTableMachine(route.name)
        const stores = props.store ?? generateItems(route.name)
        const variant = computed(() => `text-${stores.length > 0 ? 'dark' : 'white'}`)

        machines.send('submit')
        onMounted(async () => {
            await stores.fetch()
            machines.send('success')
        })

        onUnmounted(() => {
            stores.dispose()
        })
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
            const head = [h('h1', {class: 'col'}, [
                h(resolveComponent('Fa'), {brands: props.brands, icon: props.icon}),
                h('span', {class: 'ms-2'}, props.title)
            ])]
            if (typeof context.slots.btn === 'function')
                head.push(h('div', {class: 'col-1 justify-content-end'}, context.slots.btn()))
            return h(
                resolveComponent('AppOverlay'),
                {class: variant.value, id: route.name, spinner: tableLoading.some(machines.state.value.matches)},
                () => [
                    h('div', {class: 'row'}, head),
                    h(AppTable, {fields: props.fields, id: `${route.name}-table`, machine: machines, store: stores}, children)
                ]
            )
        }
    }
}
