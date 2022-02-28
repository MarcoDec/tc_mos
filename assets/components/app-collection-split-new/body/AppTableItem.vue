<script lang="ts" setup>
import { computed, defineProps, inject, reactive } from "vue";
import { Items } from "../../../store/supplierItems/supplierItem/getters";
import { TableField } from "../../../types/app-collection-table";
import { FormValue, FormValues } from "../../../types/bootstrap-5";

const emit =
  defineEmits<(e: "update", payload: { item: Items; index: number }) => void>();
const props = defineProps<{ item: Items; index: number }>();
const fields = inject<TableField[]>("fields", []);

function update(payload: { name: string; value: FormValue }): void {
  emit("update", {
    index: props.index,
    item: { ...props.item, [payload.name]: payload.value },
  });
}
</script>

<template>
  <tr>
    <td class="text-center">{{ index + 1 }}</td>

    <AppTableItemInput
      v-for="field in fields"
      :field="field"
      :key="field.name"
      :item="item"
      @input="update"
    />
  </tr>
</template>
