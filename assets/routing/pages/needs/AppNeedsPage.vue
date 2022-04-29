<script lang="ts" setup>
    import {computed, onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import AppComponentNeeds from './AppComponentNeeds .vue'
    import AppProductNeeds from './AppProductNeeds.vue'

    const active = ref('gui-start-prod')
    const height = ref(0)

    const needs = ref()

    const heightpx = computed(() => `${height.value}px`)

    function click(tab: string): void {
        active.value = tab
    }
    function resize(): void {
        if (typeof needs.value === 'undefined') return

        const rect = needs.value.getBoundingClientRect()
        height.value = rect.top

        if (window.top !== null) {
            height.value = window.top.innerHeight - rect.top - 5
            width.value = window.top.innerWidth - 25
        }
    }
    watchPostEffect(resize)
    onMounted(() => {
        window.addEventListener('resize', resize)
    })
    onUnmounted(() => {
        window.removeEventListener('resize', resize)
    })
</script>

<template>
    <div ref="needs" class="needs w-100">
        <AppTabs id="gui-start" class="gui-start-content" @click="click">
            <AppTab
                id="gui-start-prod"
                active
                icon="route"
                title="Products Needs Synthesis">
                <div class="over">
                    <AppProductNeeds v-if="active === 'gui-start-prod'"/>
                </div>
            </AppTab>
            <AppTab
                id="gui-start-comp"
                icon="route"
                title="Components Needs Synthesis">
                <div class="over">
                    <AppComponentNeeds v-if="active === 'gui-start-comp'"/>
                </div>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
.bcontent {
  margin-top: 10px;
}
.card {
  border: 1px solid #2a2a4b;
  box-shadow: black 3px 3px;
  margin-bottom: 10px;
}
.needs {
  overflow: hidden !important;

}
.over {
  overflow: auto;
  height: v-bind("heightpx");
  max-height: v-bind("heightpx");
  min-height: v-bind("heightpx");
}

@media (min-width: 1140px) {
  .needs {
    overflow: hidden !important;

  }
}

@media (min-width: 1140px) {
  .over {
    overflow: auto;
    height: v-bind("heightpx");
    max-height: v-bind("heightpx");
    min-height: v-bind("heightpx");
  }
}
</style>
