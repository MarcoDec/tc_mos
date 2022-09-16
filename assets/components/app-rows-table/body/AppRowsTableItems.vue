<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableItem from "./AppRowsTableItem.vue";
import usePrices from "../../../stores/prices/componentSuppliers";
import AppRowsTableAddItems from "./AppRowsTableAddItems.vue";

const props = defineProps({
  items: { required: true },
  price: { required: true },
  fields: { required: true, type: Array },
});
console.log("price--->", props.price);
console.log("items--->", props.items);

</script>

<template>
  <tbody>
    <AppRowsTableItem v-for="item in items" :key="item" :item="item" />
  </tbody>
</template>
