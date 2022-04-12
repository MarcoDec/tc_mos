<script lang="ts" setup>
    import AppComponentNeeds from './AppComponentNeeds .vue'
    import AppProductNeeds from './AppProductNeeds.vue'
    import {ref} from 'vue'

    const active = ref('gui-start-prod')

    function click(tab: string): void {
        active.value = tab
    }
</script>

<template>
    <AppRow>
        <AppTabs id="gui-start" class="gui-start-content" @click="click">
            <AppTab id="gui-start-prod" active title="Products Needs Synthesis">
                <AppProductNeeds v-if="active === 'gui-start-prod'"/>
            </AppTab>
            <AppTab id="gui-start-comp" title="Components Needs Synthesis">
                <AppComponentNeeds v-if="active === 'gui-start-comp'"/>
            </AppTab>
        </AppTabs>
    </AppRow>
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
</style>
