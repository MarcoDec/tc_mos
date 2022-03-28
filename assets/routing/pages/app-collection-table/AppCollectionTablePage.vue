<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {defineProps, onMounted} from 'vue'
    import type {Actions} from '../../../store/colors'
    import {useNamespacedActions} from 'vuex-composition-helpers'
    import {useRoute} from 'vue-router'

    const items: TableItem[] = []
    const props = defineProps<{fields: TableField[], icon: string, modulePath: string, title: string}>()
    const load = useNamespacedActions<Actions>(props.modulePath, ['load']).load
    const route = useRoute()

    onMounted(load)
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
