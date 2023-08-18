<script setup>
    import AppCustomerFormShow from './AppCustomerFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useCustomerStore} from '../../../../stores/selling/customers/customers'
    import AppCustomerShowInlist from './bottom/AppCustomerShowInlist.vue'
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
            <AppSuspense><AppCustomerShowInlist/></AppSuspense>
        </template>
        <template #gui-right/>
    </AppShowGuiGen>
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>
