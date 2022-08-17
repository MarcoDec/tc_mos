<script setup>
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useTableMachine } from "../../machine";
import usePrices from "../../stores/prices/componentSuppliers";
import AppRowsTable from "../../components/app-rows-table/AppRowsTable.vue";
import generateItems from "../../stores/table/items";
import generatePrice from "../../stores/prices/componentSupplier";

const route = useRoute();
defineProps({
  fields: { required: true, type: Array }
});
const machinePrice = useTableMachine("machine-prices");
const priceItems = usePrices()
console.log("prices", priceItems);

onMounted(async () => {
  await priceItems.fetch()
  priceItems.items.map((item) => item).flat(1)
});
</script>

<template>
  <h1>Component Suppliers Prices</h1>
  <AppRowsTable id="prices" :fields="fields" :items="priceItems" />
</template>
