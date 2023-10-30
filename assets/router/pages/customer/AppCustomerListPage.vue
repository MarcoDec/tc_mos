<script setup>
    import AppCustomerPage from './AppCustomerPage.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppCustomerPage :icon="icon" :title="title"/>
    </AppSuspense>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
