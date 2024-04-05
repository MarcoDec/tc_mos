<script setup>
    import AppSuspense from '../../../AppSuspense.vue'
    import AppCustomerOrderPage from './AppCustomerOrderPage.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppCustomerOrderPage :icon="icon" :title="title"/>
    </AppSuspense>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
