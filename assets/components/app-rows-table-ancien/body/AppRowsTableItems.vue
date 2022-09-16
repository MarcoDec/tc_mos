<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableItemGuesser from "./AppRowsTableItemGuesser.vue";
import usePrices from "../../../stores/prices/componentSuppliers";


const props = defineProps({
  items: { default: () => [] },
  fields: { required: true, type: Array },
  alignFields: { required: true, type: Array },
});
const priceItems = usePrices();
//const items = priceItems.rows(props.fields)
console.log("pricesItemms--->", priceItems.rows(props.fields));
const lengths = computed(() => priceItems.items.map((item) => item.length));
const max = computed(() => Math.max(...lengths.value));
const lasts = computed(() => {
  const lastindexes = [];
  if (lengths.value.length === 0) {
    return lastindexes;
  }
  for (let i = 1, j = 2; j < lengths.value.length; i++, j++) {
    if (
      lengths.value[i] !== lengths.value[j] ||
      (lengths.value[i] === lengths.value[j] && lengths.value[i] === max.value)
    ) {
      lastindexes.push(i);
    }
  }
  const last = lengths.value.length - 1;
  if (!lastindexes.includes(last)) {
    lastindexes.push(last);
  }
  return lastindexes;
});

const fieldsByLevel = computed(() => {
  const rows = [];
  let next = [];
  let hasChild = false;
  do {
    const current = next.length > 0 ? next : props.fields;
    next = [];
    hasChild = false;
    const row = [];
    for (const field of current) {
      row.push(field);
      if (Array.isArray(field.children) && field.children.length > 0) {
        next.push(...field.children);
        hasChild = true;
      }
    }
    rows.push(row);
  } while (hasChild);
  return rows;
});

const levels = computed(() => {
  const levelObj = {};
  for (let i = fieldsByLevel.value.length - 1, j = 1; i > 0; i--, j++)
    levelObj[i] = j;
  return levelObj;
});
const itemsWithGhosts = computed(() => {
  const result = [];

  for (let i = 0, j = 1; i < priceItems.items.length; i++, j++) {

    result.push(priceItems.items[i]);
    if (j === priceItems.items.length) {
      for (const k in levels.value) {
        if (priceItems.items.length >= k) {
          result.push(levels.value[k]);
        }
      }
    }
  }
  result.push(0);

  return result; 
});
</script>

<template>
  <tbody>
    <AppRowsTableItemGuesser
      v-for="(item, index) in itemsWithGhosts"
      :key="item.id"
      :fields="fields"
      :align-fields="alignFields"
      :index="index"
      :fields-by-level="fieldsByLevel"
      :last="lasts.includes(index)"
      :item="item"
    />
  </tbody>
</template>
