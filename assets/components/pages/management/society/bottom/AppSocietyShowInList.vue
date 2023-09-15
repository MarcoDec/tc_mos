<script setup>
    import AppSocietyListCustomer from './AppSocietyListCustomer.vue'
    import AppSocietyListCompany from './AppSocietyListCompany.vue'
    import AppSocietyListSupplier from './AppSocietyListSupplier.vue'
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
        <AppTab id="gui-start-customer" active title="Customer" icon="briefcase" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListCustomer/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-company" title="Company" icon="building" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListCompany/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-supplier" title="Supplier" icon="truck-field" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSocietyListSupplier/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
