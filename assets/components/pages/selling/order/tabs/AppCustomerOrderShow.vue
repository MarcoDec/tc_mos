<script setup>
    import AppShowCustomerOrderItems from './AppShowCustomerOrderItems.vue'
    defineProps({
        order: {default: () => ({}), required: true, type: Object},
        customer: {default: () => ({}), required: true, type: Object}
    })
</script>

<template>
    <AppShowCustomerOrderItems :customer="customer" :order="order"/>
</template>
