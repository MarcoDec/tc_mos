<script setup>
import { defineProps } from "vue";
import AppPricesTableItem from "./AppPricesTableItem.vue";

const props = defineProps({
  items: { required: true, type: Object },
  fieldsComponenentSuppliers: { required: true, type: Array },
  fieldsComponenentSuppliersPrices: { required: true, type: Array }
});
</script>

<template>
    <AppPricesTableItem v-for="item in items" :key="item" :item="item" :items="items" :fieldsComponenentSuppliers="fieldsComponenentSuppliers" :fieldsComponenentSuppliersPrices="fieldsComponenentSuppliersPrices"/>
</template>
