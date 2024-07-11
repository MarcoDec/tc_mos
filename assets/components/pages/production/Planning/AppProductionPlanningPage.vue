<script setup>
    import {defineProps} from 'vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppProductionPlanningTable from './AppProductionPlanningTable.vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppSuspense>
        <AppProductionPlanningTable :fields="fields"/>
    </AppSuspense>
</template>

