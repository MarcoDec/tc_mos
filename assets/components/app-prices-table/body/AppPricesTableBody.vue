<script setup>
import { defineProps } from "vue";
import AppPricesTableAddItems from "./AppPricesTableAddItems.vue";
import AppPricesTableItems from "./AppPricesTableItems.vue";

const props = defineProps({
  fieldsComponenentSuppliers: { required: true, type: Array },
  fieldsComponenentSuppliersPrices: { required: true, type: Array },
  items: { required: true, type: Object }
});
</script>

<template>
  <tbody>
    <AppPricesTableItems  :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices" :items="items"/>
    <AppPricesTableAddItems :fields="fieldsComponenentSuppliers" />
  </tbody>
</template>
