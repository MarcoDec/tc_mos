<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/warehouseListItems'
    import {defineProps, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TableField} from '../../../types/app-collection-table'
    import {useRoute} from 'vue-router'

    const props= defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()
    const lengthFields =  props.fields.length

    const fetchField = useNamespacedActions<Actions>('productionPlannings', ['fetchField']).fetchField
    const {apiFields} = useNamespacedGetters<Getters>('productionPlannings', ['apiFields'])

    const fetchItem = useNamespacedActions<Actions>('productionPlanningsItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('productionPlanningsItems', ['items'])
    onMounted(async () => {
        await fetchField()
        await fetchItem()
    })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <div class="tableFixHead">
        <AppScheduleTable :id="route.name" :lengthFields="lengthFields" :fields="fields" :api-fields="apiFields" :items="items"/>
    </div>
</template>
