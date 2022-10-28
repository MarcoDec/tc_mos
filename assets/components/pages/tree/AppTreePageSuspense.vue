<script setup>
    /* eslint-disable vue/no-unused-properties */
    import AppTreePage from './AppTreePage.vue'

    defineProps({label: {required: true, type: String}})
</script>

<template>
    <AppSuspense>
        <AppTreePage v-bind="$props"/>
    </AppSuspense>
</template>
