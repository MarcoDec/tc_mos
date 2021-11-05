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
    import {defineProps} from 'vue'
    import AppRow from '../bootstrap-5/layout/AppRow.vue'
    import AppShowGuiCard from './AppShowGuiCard.vue'


    defineProps<{cssClass?: string}>()
</script>


<style lang="scss" scoped>
.gui {
  width: var(--width);

}

.gui-container {
  background-color: #cecdcd;
  border-radius: 15px;
}

.gui-up-left {
  padding: 6px 2px 3px 6px;
  overflow: hidden;
}

.gui-up-right {
  padding: 6px 6px 3px 2px;
}

.gui-down {
  padding: 3px 6px 6px;
}
</style>
