<script setup>
    import {computed, ref} from 'vue'
    import AppSocietyListCustomer from './AppSocietyListCustomer.vue'
    import AppSocietyListCompany from './AppSocietyListCompany.vue'
    import AppSocietyListSupplier from './AppSocietyListSupplier.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <h1>Customer</h1>
        <AppTab id="gui-start-customer" active title="Customer" icon="book" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListCustomer/>
            </AppSuspense>
        </AppTab>
        <h1>Company</h1>
        <AppTab id="gui-start-company" active title="Company" icon="calendar" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListCompany/>
            </AppSuspense>
        </AppTab>
        <h1>Supplier</h1>
        <AppTab id="gui-start-supplier" active title="Supplier" icon="file-invoice" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListSupplier/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
