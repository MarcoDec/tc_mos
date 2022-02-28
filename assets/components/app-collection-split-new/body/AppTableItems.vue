<script lang="ts" setup>
import { computed, defineProps, inject, reactive } from "vue";
import { Items } from "../../../store/supplierItems/supplierItem/getters";
import { TableField } from "../../../types/app-collection-table";

const props = defineProps<{ item: Items; field: TableField }>();
const fields = inject<TableField[]>("fields", []);

const items = reactive<Items[]>([props.item]);
const total = computed(() =>
  items.reduce(
    (previousValue, currentValue) =>
      previousValue + (currentValue.quantite as number),
    0
  )
);
const rest = computed(() => {
  const result = (props.item.quantite ?? 0) - total.value;
  return result > 0 ? result:0
});

console.log("total", total.value);
console.log("reste", rest.value);

function plus(item: Items): void {
  items.push(item);
}
function update(payload: { item: Items; index: number }): void {
  items[payload.index] = payload.item;
  console.log("je suis ici-->", payload);
  console.log("je s-->", props.item.quantite);
}
</script>

<template>
  <tbody>
    <AppTableItem
      v-for="(current, i) in items"
      :index="i"
      :key="i"
      :item="current"
      @update="update"
    />
    <AppTableAddItem :value="rest" @plus="plus" />
  </tbody>
</template>
