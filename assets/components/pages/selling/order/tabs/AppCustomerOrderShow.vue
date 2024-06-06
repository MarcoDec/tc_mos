<script setup>
    import AppShowCustomerOrderItems from './AppShowCustomerOrderItems.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    defineProps({
        order: {default: () => ({}), required: true, type: Object},
        customer: {default: () => ({}), required: true, type: Object}
    })
</script>

<template>
    <AppSuspense>
        <AppShowCustomerOrderItems :customer="customer" :order="order"/>
    </AppSuspense>
</template>
