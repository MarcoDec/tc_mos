<script setup>
    import AppProductPage from './AppProductPage.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppProductPage :icon="icon" :title="title"/>
    </AppSuspense>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
