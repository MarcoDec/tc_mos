<template>
  <AppRow :style="width" class="gui">
    <AppCol>
      <AppContainer class="gui-container mt-2" fluid>

        <AppRow>
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="info"
              class="gui-up-left"
              css-class="gui-top">
            <div class="tab-header">
              <AppTabs>
                <AppTab name="First tab">
                  First tab content
                </AppTab>
                <AppTab name="Second tab">
                  Second tab content
                </AppTab>
                <AppTab name="Third tab">
                  Third tab content
                </AppTab>
              </AppTabs>
            </div>

          </AppShowGuiCard>
          <AppShowGuiCard
              :css-style="topHeight"
              bg-variant="warning"
              class="gui-up-right"
              css-class="gui-top">


          </AppShowGuiCard>

        </AppRow>
        <AppRow>
          <AppShowGuiCard
              :css-style="bottomHeight"
              bg-variant="success"
              class="gui-down"
              css-class="gui-bottom">

          </AppShowGuiCard>
        </AppRow>

      </AppContainer>
    </AppCol>

  </AppRow>

</template>

<script lang="ts" setup>
import {defineProps} from 'vue'
import AppRow from "../bootstrap-5/layout/AppRow.vue";
import AppCol from "../bootstrap-5/layout/AppCol.vue";
import AppShowGuiCard from "./AppShowGuiCard.vue";
import AppContainer from "../bootstrap-5/layout/AppContainer.vue";

defineProps<{ cssClass?: string }>()
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

.tab-header {
  padding: 0.75rem 1.25rem;
  margin-bottom: 0;
  background-color: rgba(0, 0, 0, 0.03);
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

</style>
