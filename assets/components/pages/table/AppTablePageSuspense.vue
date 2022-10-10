<script setup>
    import AppTablePage from './AppTablePage.vue'

    defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppTablePage :fields="fields" :icon="icon" :title="title"/>
    </AppSuspense>
</template>
