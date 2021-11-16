<template>
  <AppRow :style="width" class="gui">
    <AppCol>
      <AppContainer class="gui-container mt-2" fluid>
        <AppRow :class="cssClass">
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="info"
              class="gui-up-left"
              css-class="gui-top">
            <AppShowGuiTabs/>
          </AppShowGuiCard>
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="warning"
              class="gui-up-right"
              css-class="gui-top"/>
        </AppRow>
        <AppRow>
          <AppShowGuiCard
              :css-style="bottomHeight"
              bg-variant="success"
              class="gui-down"
              css-class="gui-bottom"/>
        </AppRow>
      </AppContainer>
    </AppCol>
  </AppRow>
</template>

<script lang="ts" setup>
import AppContainer from '../bootstrap-5/layout/AppContainer.vue'
import AppCol from '../bootstrap-5/layout/AppCol.vue'
import {computed, defineProps, onMounted, onUnmounted, ref} from 'vue'
import AppRow from '../bootstrap-5/layout/AppRow.vue'
import AppShowGuiCard from './AppShowGuiCard.vue'


defineProps<{ cssClass?: string , card}>()


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

.gui {
  width: v-bind(containerWidth+px);
}

.gui-container {
  background-color: #cecdcd;
  border-radius: 15px;
}

.gui-up-left {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border: 5px solid #17a2b8;
  border-radius: 10px;
  margin-right: 5px;
  margin-bottom: 5px;
  margin-top: 5px;
}

.gui-up-right {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border-radius: 10px;
  border: 5px solid #ffc107;
  margin-bottom: 5px;
  margin-top: 5px;
}

.gui-down {
  padding: 0px 0px 0px 0px;
  overflow: hidden;
  border-radius: 10px;
  border: 5px solid #28a745;
  margin-bottom: 5px;
}
</style>
