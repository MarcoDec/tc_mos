

<template>
  <div :class="cssClass" class="card">
    <div class="card-body">
      <slot/>
    </div>
  </div>
</template>



<script lang="ts" setup>
import { defineProps, onMounted, onUnmounted} from 'vue'
import {useNamespacedGetters, useNamespacedMutations, useNamespacedState} from "vuex-composition-helpers";
import {Getters} from "../../../store/gui/getters";
import {Mutations, MutationTypes} from "../../../store/gui/mutations";


defineProps<{ cssClass?: string }>()
const {heightBottomInnerpx,maxInnerHeightpx} = useNamespacedGetters<Getters>('gui',['heightBottomInnerpx','maxInnerHeightpx'])

const resize = useNamespacedMutations<Mutations>('gui',[MutationTypes.RESIZE])[MutationTypes.RESIZE]

onMounted(() => {
  window.addEventListener('resize', resize)
  resize()
})
onUnmounted(() => {
      window.removeEventListener('resize', resize)
    }
)

</script>


<style lang="scss" scoped>


.card-body {
  padding: 4px;
  max-height: v-bind(heightBottomInnerpx);
  min-height: v-bind(heightBottomInnerpx);

  > div {
    background-color: white;
    height: v-bind(maxInnerHeightpx);
    max-height: v-bind(maxInnerHeightpx);
    overflow: hidden;
  }
}

</style>


