<script setup>
import {defineProps } from "vue";
import AppPricesTableHeaders from "./head/AppPricesTableHeaders.vue";
import AppPricesTableBody from "./body/AppPricesTableBody.vue";


const props = defineProps({
  fieldsComponenentSuppliers: { required: true, type: Array },
  fieldsComponenentSuppliersPrices: { required: true, type: Array },
  id: { required: true, type: String },
  items: { required: true, type: Object },
});
</script>

<template>
  <table :id="id" class="table table-bordered table-hover table-striped">
    <AppPricesTableHeaders :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices"/>
    <AppPricesTableBody :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices" :items="items"/>
  </table>
  <slot name="btn"/>
</template>
