<script setup>
    import {tableLoading, useTableMachine} from '../../../composable/xstate'
    import AppTable from '../../table/AppTable.vue'
    import {onUnmounted} from 'vue'
    import {useRoute} from 'vue-router'
    import useTable from '../../../stores/table/table'

    defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const route = useRoute()
    const {send, state} = useTableMachine(route.name)
    const store = useTable(route.name)

    await store.fetch()

    onUnmounted(() => {
        store.dispose()
    })
</script>

<template>
    <AppOverlay :spinner="tableLoading.some(state.matches)">
        <div class="row">
            <h1 class="col">
                <Fa :icon="icon"/>
                <span class="ms-2">{{ title }}</span>
            </h1>
        </div>
        <AppTable :fields="fields" :send="send" :store="store"/>
    </AppOverlay>
</template>
