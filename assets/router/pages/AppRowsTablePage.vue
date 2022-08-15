<script setup>
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useTableMachine } from "../../machine";
import usePrices from "../../stores/prices/componentSuppliers";
import AppRowsTable from "../../components/app-rows-table/AppRowsTable.vue";
import generateItems from "../../stores/table/items";

const route = useRoute();
defineProps({
  fields: { required: true, type: Array }
});
const machinePrice = useTableMachine("machine-prices");
//const priceItems = generateItems("prices-items");
const priceItems = usePrices()
console.log("prices", priceItems);

onMounted(async () => {
  await priceItems.fetch()

 /* priceItems.items = [
    {
      delai: 5,
      delete: true,
      id: 1,
      indice: 1,
      moq: 1,
      name: "CAMION FR-MD",
      poidsCu: "bbbbb",
      prices: [
        {
          delete: false,
          id: 1,
          price: 1000,
          quantite: 100,
          ref: "afsfsfss",
          update: true,
          update2: false,
        },
        {
          delete: false,
          id: 3,
          price: 1000,
          quantite: 30,
          ref: "azertsscssy",
          update: true,
          update2: false,
        },
      ],
      proportion: "aaaaaa",
      reference: "ccc",
      update: true,
      update2: false,
    },
    {
      delai: 15,
      delete: true,
      id: 2,
      indice: 2,
      moq: 2,
      name: "CAMION",
      poidsCu: "aaaa",
      prices: [
        {
          delete: false,
          id: 2,
          price: 100,
          quantite: 50,
          ref: "azerty",
          update: true,
          update2: false,
        },
      ],
      proportion: "vvvvvv",
      reference: "wwwww",
      update: true,
      update2: false,
    },
  ];*/
});
</script>

<template>
  <h1>Component Suppliers Prices</h1>
  <AppRowsTable id="prices" :fields="fields" :items="priceItems" />
</template>
