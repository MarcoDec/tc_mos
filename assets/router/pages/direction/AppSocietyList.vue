<script setup>
    import AppSocietyListTable from './AppSocietyListTable.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppSocietyListTable :icon="icon" :title="title"/>
    </AppSuspense>
</template>
