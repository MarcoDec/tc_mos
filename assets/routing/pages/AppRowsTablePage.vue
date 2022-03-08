<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-rows-table'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import {defineProps, onMounted} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[]}>()
    const route = useRoute()

    const fetchItem = useNamespacedActions<Actions>('componentSuppliers', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('componentSuppliers', ['items'])
    onMounted(async () => {
        await fetchItem()
    })
    console.log('items',items);
    

</script>

<template>
    <AppRowsTable :id="route.name" :fields="fields" :items="items" create/>
</template>