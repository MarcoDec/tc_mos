<script setup>
    import AppSuspense from '../../../components/AppSuspense.vue'
    import AppPricesTablePage from './AppPricesTablePage.vue'
</script>

<template>
    <AppSuspense>
        <AppPricesTablePage
            component="/api/components/667"
            title="Component Supplier Prices"
        />
    </AppSuspense>
</template>
