<script lang="ts" setup>
import {defineProps, onMounted, onUnmounted} from 'vue'
import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'
import AppCol from '../bootstrap-5/layout/AppCol.vue'
import AppCard from '../bootstrap-5/card/AppCard.vue'
import type {Getters} from '../../store/gui/getters'
import {MutationTypes} from '../../store/gui/mutations'
import type {Mutations} from '../../store/gui/mutations'


defineProps<{ cssClass?: string }>()

const {heightBottompx, heightTopInnerpx} = useNamespacedGetters<Getters>('gui', ['heightBottompx', 'heightTopInnerpx'])

const resize = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])[MutationTypes.RESIZE]

onMounted(() => {
  window.addEventListener('resize', resize)
  resize()
})
onUnmounted(() => {
  window.removeEventListener('resize', resize)
})
</script>

<template>
  <AppCol>
    <AppCard :class="cssClass" border-variant="secondary">
      <slot/>
    </AppCard>
  </AppCol>
</template>


<style lang="scss" scoped>
.card {
  //border-radius: 10px;
  overflow: hidden;

  &.gui-bottom {
    height: v-bind(heightTopInnerpx);
    min-height: v-bind(heightBottompx);
    max-height: v-bind(heightTopInnerpx);
  }

  &.gui-top {
    height: v-bind(heightTopInnerpx);
  }
}
</style>


