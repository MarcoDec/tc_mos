<script lang="ts" setup>
    import type {Actions, Getters} from '../../store/componentSuppliers'
    import {defineProps, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TableField} from '../../types/app-rows-table'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[]}>()
    const route = useRoute()

    const fetchItem = useNamespacedActions<Actions>('componentSuppliers', ['fetchItem']).fetchItem
    //const {items} = useNamespacedGetters<Getters>('componentSuppliers', ['items'])
    const {rows} = useNamespacedGetters<Getters>('componentSuppliers', ['rows'])

    onMounted(async () => {
        await fetchItem()
    })
</script>

<template>
    <AppRowsTable :id="route.name" :fields="fields" :items="rows" create/>
</template>
