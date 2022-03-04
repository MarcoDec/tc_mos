<script lang="ts" setup>
import { ref } from "@vue/reactivity";
import { onMounted, onUnmounted } from "@vue/runtime-core";
import { useActions } from "vuex-composition-helpers";
import { Actions } from "../../../../store";
import { generateSupplier } from "../../../../store/suppliers/supplier";


const wrapper = ref(false)
const modulePath: [string, ...string[]] = ['suppliers']

const { registerModule, unregisterModule } = useActions<Actions>([
  "registerModule",
  "unregisterModule",
]);

onMounted(async () => {
 /* await registerModule({
    module: generateSupplier(modulePath),
    path: modulePath,
  });*/
  wrapper.value = true;
});
onUnmounted(async () => {
  await unregisterModule(modulePath);
});
</script>

<template>
  <AppSupplierPage v-if="wrapper">
  </AppSupplierPage>
</template>
