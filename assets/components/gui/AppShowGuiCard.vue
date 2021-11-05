<template>
    <AppCol>
        <AppCard :class="cssClass" :style="cssStyle" border-variant="secondary">
            <slot/>
        </AppCard>
    </AppCol>
</template>


<script lang="ts" setup>
    import {defineProps} from 'vue'
    import AppCol from '../bootstrap-5/layout/AppCol.vue'
    import AppCard from '../bootstrap-5/card/AppCard.vue'

    defineProps<{cssClass?: string}>()
</script>


<style lang="scss" scoped>
.card {
  border-radius: 10px;
  overflow: hidden;
  --height: 341.28px;
  --innerHeight: 331.28px;

  &.gui-bottom {
    height: var(--height);
    min-height: var(--height);
    max-height: var(--height);
  }

  &.gui-top {
    height: var(--innerHeight);
  }
}

.card-body {
  padding: 4px;
  max-height: var(--innerHeight);
  min-height: var(--innerHeight);

  > div {
    background-color: white;
    height: var(--maxInnerHeight);
    max-height: var(--maxInnerHeight);
    overflow: hidden;
  }
}
</style>


