<template>
  <AppCol>
    <AppCard :class="cssClass" :style="cssStyle" border-variant="secondary">
      <slot/>
    </AppCard>
  </AppCol>
</template>


<script lang="ts" setup>
import {computed, defineProps, onMounted, onUnmounted, ref} from 'vue'
import AppCol from '../bootstrap-5/layout/AppCol.vue'
import AppCard from '../bootstrap-5/card/AppCard.vue'

defineProps<{ cssClass?: string }>()

const windowHeight = ref(0)
const windowWidth = ref(0)
const freeSpace = ref(.9)
const topRatio = ref(.6)


const containerHeight = computed(() => ((windowHeight.value - 90) * freeSpace.value))
const containerWidth = computed(() => (windowWidth.value - 5))
const bottomRatio = computed(() => (1 - topRatio.value))
const heightBottom = computed(() => (containerHeight.value - 12) * bottomRatio.value)
const heightBottomInner = computed(() => (heightBottom.value - 3))
const heightTop = computed(() => (containerHeight.value * topRatio.value))
const heightTopInner = computed(() => (heightTop.value - 10))


const heightBottompx = computed(() => (heightBottom.value+'px'))
const heightTopInnerpx = computed(() => (heightTopInner.value+'px'))



function resize(): void {
  if (window.top !== null) {
    windowHeight.value = window.top.innerHeight
    windowWidth.value = window.top.innerWidth

  }

}

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

.card {
  border-radius: 10px;
  overflow: hidden;
  //--height: 341.28px;
  // --innerHeight: 331.28px;
  &.gui-bottom {
    height: v-bind(heightBottompx);
    min-height: v-bind(heightBottompx);
    max-height: v-bind(heightBottompx);
  }

  &.gui-top {
    height: v-bind(heightTopInner)px;
  }
}

.card-body {
  padding: 4px;
  max-height: v-bind(heightTopInnerpx);
  min-height: v-bind(heightTopInnerpx);

  > div {
    background-color: white;
    height: var(--maxInnerHeight);
    max-height: var(--maxInnerHeight);
    overflow: hidden;
  }
}

</style>


