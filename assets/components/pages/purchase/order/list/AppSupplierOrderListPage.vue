<script setup>
    import AppSupplierOrderPage from './AppSupplierOrderPage.vue'
    import AppSuspense from "../../../../AppSuspense.vue"

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppSupplierOrderPage :icon="icon" :title="title"/>
    </AppSuspense>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
