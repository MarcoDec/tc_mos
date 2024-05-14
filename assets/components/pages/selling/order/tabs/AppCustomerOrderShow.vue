<script setup>
    import AppShowCustomerOrderItems from './AppShowCustomerOrderItems.vue'
    defineProps({
        order: {default: () => {}, required: true, type: Object}
    })
</script>

<template>
    <AppShowCustomerOrderItems :order="order"/>
</template>
