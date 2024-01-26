<script setup>
    import AppEngineListTable from './AppEngineListTable.vue'
    import AppSuspense from '../../../../AppSuspense.vue'

    defineProps({
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppEngineListTable :title="title"/>
    </AppSuspense>
</template>
