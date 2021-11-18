

<template>
  <div :class="cssClass" class="card">
    <div class="card-body">
      <slot/>
    </div>
  </div>
</template>



<script lang="ts" setup>
import {computed, defineProps, onMounted, onUnmounted, ref} from 'vue'
import {useNamespacedState} from "vuex-composition-helpers";


defineProps<{ cssClass?: string }>()


/*const windowHeight = ref(0)
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

const heightBottomInnerpx = computed(() => (heightBottomInner.value+'px'))

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
)*/
</script>


<style lang="scss" scoped>


.card-body {
  padding: 4px;
  max-height: v-bind(heightBottomInnerpx);
  min-height: v-bind(heightBottomInnerpx);

  > div {
    background-color: white;
    height: var(--maxInnerHeight);
    max-height: var(--maxInnerHeight);
    overflow: hidden;
  }
}

</style>


