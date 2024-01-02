<script setup>
    import AppEmployeePage from './AppEmployeePage.vue'
    import AppSuspense from '../../../AppSuspense.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppEmployeePage :icon="icon" :title="title"/>
    </AppSuspense>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
