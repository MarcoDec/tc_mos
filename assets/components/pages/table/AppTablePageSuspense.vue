<script setup>
    import AppTablePage from './AppTablePage.vue'

    defineProps({
        fields: {required: true, type: Object},
        icon: {required: true, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppTablePage :fields="fields" :icon="icon" :sort="sort" :title="title"/>
    </AppSuspense>
</template>
