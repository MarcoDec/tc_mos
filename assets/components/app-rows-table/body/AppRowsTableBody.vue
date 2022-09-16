<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableAddItems from "./AppRowsTableAddItems.vue";
import AppRowsTableItems from "./AppRowsTableItems.vue";
import usePrices from "../../../stores/prices/componentSuppliers";

const props = defineProps({
  fields: { required: true, type: Array },
});
const priceItems = usePrices();
console.log("items", priceItems.items);
console.log("rows", priceItems.rows(props.fields));

</script>

<template>
  <AppRowsTableItems :items="priceItems.items" :fields="fields"/>
  <AppRowsTableAddItems :fields="fields" />
</template>
