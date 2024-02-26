import {h, resolveComponent} from 'vue'

function AppPagination(props, context) {
    const range = Array.from(Array(props.store.pages + 1).keys())
    range.shift()
    return h(
        'nav',
        h(
            'ul',
            {class: 'pagination'},
            typeof context.slots.default === 'function'
                ? context.slots.default({machine: props.machine, range, store: props.store})
                : range.map(index => h(
                    resolveComponent('AppPaginationItem'),
                    {
                        index,
                        async onClick() {
                            props.machine.send('submit')
                            await props.store.goTo(index)
                            props.machine.send('success')
                        },
                        store: props.store
                    }
                ))
        )
    )
}

AppPagination.props = {machine: {required: true, type: Object}, store: {required: true, type: Object}}

export default AppPagination
