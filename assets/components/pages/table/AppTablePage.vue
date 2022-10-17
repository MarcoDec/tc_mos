<script setup>
    /* eslint-disable vue/no-setup-props-destructure */
    import {tableLoading, useTableMachine} from '../../../composable/xstate'
    import AppTable from '../../table/AppTable.vue'
    import {onUnmounted} from 'vue'
    import useFields from '../../../stores/field/fields'
    import {useRoute} from 'vue-router'
    import useTable from '../../../stores/table/table'

    const props = defineProps({
        disableRemove: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const machine = useTableMachine(route.name)
    const storedFields = useFields(route.name, props.fields)

    const store = useTable(route.name)
    store.sorted = props.sort.name
    store.sortName = props.sort.sortName ?? props.sort.name
    await store.fetch()

    const options = []
    for (const field of storedFields.fields)
        if (field.options)
            options.push(field.options.fetch())
    await Promise.all(options)

    onUnmounted(() => {
        store.dispose()
        for (const field of storedFields.fields)
            if (field.options)
                field.options.dispose()
    })
</script>

<template>
    <AppOverlay :spinner="tableLoading.some(machine.state.value.matches)">
        <div class="row">
            <h1 class="col">
                <Fa :icon="icon"/>
                <span class="ms-2">{{ title }}</span>
            </h1>
        </div>
        <AppTable
            :id="route.name"
            :disable-remove="disableRemove"
            :fields="storedFields"
            :machine="machine"
            :store="store"/>
    </AppOverlay>
</template>
