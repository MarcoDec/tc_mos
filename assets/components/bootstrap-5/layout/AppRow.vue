<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{cssClass?: string}>()
</script>

<template>
    <div :class="cssClass" class="row">
        <slot/>
    </div>
</template>


<style lang="scss" scoped>
@media only screen and (min-width: 150px) and (max-width: 1024px) {
  .row{
    display: block;
  }
}
</style>
