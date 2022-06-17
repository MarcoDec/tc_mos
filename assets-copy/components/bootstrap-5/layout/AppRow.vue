<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{cssClass?: string}>()
</script>

<template>
    <div :class="cssClass" class="row">
        <slot/>
    </div>
</template>
