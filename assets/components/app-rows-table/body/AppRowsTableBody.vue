<script setup>
import { defineProps } from "vue";
import AppRowsTableAddItems from "./AppRowsTableAddItems.vue";
import AppRowsTableItems from "./AppRowsTableItems.vue";
// import usePrices from "../../../stores/prices/componentSuppliers";

const props = defineProps({
  fields: { required: true, type: Array },
  // items: { required: true, type: Object }
});
// const priceItems = usePrices();
console.log("items", props.items);
// console.log("rows", priceItems.rows(props.fields));

</script>

<template>
  <!-- <AppRowsTableItems :items="priceItems.items" :fields="fields"/>  -->
  <AppRowsTableItems  :fields="fields" /> 
  <AppRowsTableAddItems :fields="fields" />
</template>
