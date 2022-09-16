<script setup>
import { computed, defineProps, ref } from "vue";

const props = defineProps({
  state: { required: false, type: Array },
  i: { required: false, type: Number },
  stateFields: { required: false, type: Array },
});
console.log('je suis ici', props.state);
const show = ref(true);
const td = computed(() =>
  show.value ? "AppRowsTableItemField" : "AppRowsTableItemInput"
);

function toggle() {
  show.value = !show.value;
}
</script>

<template>
  <td :rowspan="state.rowspan">
    {{ state.index }}
  </td>
  <td v-if="show" :rowspan="state.rowspan" class="text-center">
    <AppBtn
      v-if="state.update"
      icon="pencil-alt"
      variant="primary"
      @click="toggle"
    />
    <AppBtn v-if="state['delete']" icon="trash" variant="danger" />
  </td>
  <td v-else :rowspan="state.rowspan" class="text-center">
    <AppBtn icon="check" variant="success" />
    <AppBtn icon="times" variant="danger" @click="toggle" />
  </td>
  <component
    :is="td"
    v-for="field in stateFields[i]"
    :key="field.name"
    :rowspan="state.rowspan"
    :field="field"
    :item="state"
  />
</template>
