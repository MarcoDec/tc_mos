<script setup>
    import AppEngineListTable from './AppEngineListTable.vue'
    import AppSuspense from '../../../../AppSuspense.vue'

    defineProps({
        title: {required: true, type: String},
        engineType: {required: true, type: String},
        icon: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppEngineListTable :title="title" :engine-type="engineType" :icon="icon"/>
    </AppSuspense>
</template>
