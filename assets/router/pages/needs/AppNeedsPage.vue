<script setup>
    import {ref} from 'vue'
    import AppComponentNeeds from './AppComponentNeeds.vue'
    import AppProductNeeds from './AppProductNeeds.vue'

    const active = ref('gui-start-prod')

    function click(tab) {
        active.value = tab
    }
</script>

<template>
    <div class="needs w-100">
        <AppTabs id="gui-start" class="gui-start-content" @click="click">
            <AppTab
                id="gui-start-prod"
                active
                icon="route"
                tabs="gui-start"
                title="Products Needs Synthesis">
                <AppProductNeeds/>
            </AppTab>
            <AppTab
                id="gui-start-comp"
                icon="route"
                tabs="gui-start"
                :active="active === 'gui-start-comp'"
                title="Components Needs Synthesis">
                <AppComponentNeeds/>
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
    }

    @media (min-width: 1140px) {
      .needs {
        overflow: hidden !important;
      }
    }

    @media (min-width: 1140px) {
      .over {
        overflow: auto;
      }
    }
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
        .gui-start-content {
            font-size: 14px;
        }
</style>
