
<script setup>
    import AppSocityListTable from './AppSocityListTable.vue'
    import AppSuspense from '../../../AppSuspense.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppSocityListTable :icon="icon" :title="title"/>
    </AppSuspense>
</template>
