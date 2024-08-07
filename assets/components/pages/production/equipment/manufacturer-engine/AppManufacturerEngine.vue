<script setup>
    import AppManufacturerEngineListTable from './AppManufacturerEngineListTable.vue'
    import AppSuspense from '../../../../AppSuspense.vue'

    defineProps({
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppManufacturerEngineListTable :title="title"/>
    </AppSuspense>
</template>
