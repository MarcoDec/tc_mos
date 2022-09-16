<script setup>
import { computed, defineProps } from "vue";
import usePrices from "../../../stores/prices/componentSuppliers";
import AppRowsTableItems from "./AppRowsTableItems.vue";

const props = defineProps({
  item: { required: false },
  fields: { required: true, type: Array },
  items: { required: true, type: Object },
});

const priceItems = usePrices();
console.log("je suis ici", props.item);

</script>

<template>
  <td></td>
  <AppRowsTableItems
    v-for="(price, i) in item.prices"
    :key="i"
    :price="price"
    :fields="fields"
    :items="items"
  />
  <AppRowsTableAddItems :fields="fields" />
</template>
