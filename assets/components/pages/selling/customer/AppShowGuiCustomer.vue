<script setup>
    import AppCustomerFormShow from '../../../../router/pages/customer/AppCustomerFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useCustomerStore} from '../../../../stores/customers/customers'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idCustomer = Number(route.params.id_customer)
    const fetchCustomerStore = useCustomerStore()
    fetchCustomerStore.fetchOne(idCustomer)
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div class="bg-white border-1 border-dark">
                <b>Client ({{ fetchCustomerStore.customer.id }})</b>: {{ fetchCustomerStore.customer.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppCustomerFormShow v-if="fetchCustomerStore.isLoaded"/></AppSuspense>
        </template>
        <template #gui-bottom>
            <!--            <AppTabs id="gui-bottom">-->
            <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
            <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
            <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
            <!--            </AppTabs>-->
        </template>
        <template #gui-right/>
    </AppShowGuiGen>
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>
