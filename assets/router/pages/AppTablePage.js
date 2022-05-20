import {computed, h, onMounted, onUnmounted, resolveComponent} from 'vue'
import AppTable from '../../components/table/AppTable'
import {generateTableFields} from '../../components/validators'
import {useRoute} from 'vue-router'
import {useTableMachine} from '../../machine'

const loading = ['create.loading', 'search.loading', 'update.loading']

export default {
    props: {
        fields: generateTableFields(),
        icon: {required: true, type: String},
        store: {required: true, type: Function},
        title: {required: true, type: String}
    },
    async setup(props, context) {
        onMounted(async () => {
            await store.fetch()
            machine.send('success')
        })

        onUnmounted(() => {
            store.dispose()
        })

        const route = useRoute()
        const machine = useTableMachine(route.name)
        const module = await props.store()
        const store = module['default']()
        const variant = computed(() => `text-${store.length > 0 ? 'dark' : 'white'}`)

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
                {class: variant.value, id: route.name, spinner: loading.some(machine.state.value.matches)},
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
