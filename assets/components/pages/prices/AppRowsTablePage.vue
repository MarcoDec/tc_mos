<script setup>
import {computed} from 'vue-demi'
import {useComponentSuppliersStore} from '../../../stores/prices/componentSuppliers';
import AppPricesTable from "../../app-prices-table/AppPricesTable.vue";
import AppSuspense from '../../AppSuspense.vue'


defineProps({
  fieldsComponenentSuppliers: { required: true, type: Array },
  fieldsComponenentSuppliersPrices: { required: true, type: Array }
});
const storeComponentSuppliers = useComponentSuppliersStore()
await storeComponentSuppliers.fetchByComponent(667)
await storeComponentSuppliers.fetchPricesForItems()
const componentSuppliersItems = computed(() => storeComponentSuppliers.componentSuppliersItems)
</script>

<template>
  <AppSuspense>
    <h1>Component Suppliers Prices</h1>
    <AppPricesTable id="prices" :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices" :items="componentSuppliersItems"/>
  </AppSuspense>
</template>
