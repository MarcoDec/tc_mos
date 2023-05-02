<script setup>
import { defineEmits, defineProps } from "vue";

const props = defineProps({
  fields: { required: true, type: Array },
});
const inputValues = {};
const emit = defineEmits(["close", "ajout"]);

function bascule() {
  emit("close");
}
function ajout() {
  emit("ajout", inputValues);
}
</script>

<template>
  <tr class="addrow">
    <th scope="row">
      <Fa icon="plus" />
    </th>
    <td>
      <AppBtn icon="search" label="Ajouter" @click="bascule" />
      <AppBtn icon="plus" label="Modifier" @click="ajout" />
    </td>
    <td v-for="field in fields" :key="field.name">
      <AppInputGuesserJS
        :id="field.name"
        v-model="inputValues[field.name]"
        :form="form"
        :field="field"
        :update:model-value="modelValue"
      />
    </td>
  </tr>
</template>

<style scoped>
.addrow {
  background-color: #c9ffc1;
}
.btn {
  width: 24px;
  height: 24px;
  padding-left: 4px;
  padding-bottom: 24px;
}
</style>
