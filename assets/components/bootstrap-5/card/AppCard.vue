<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {BootstrapVariant} from '../../../types/bootstrap-5'

    const props = defineProps<{bgVariant?: BootstrapVariant}>()
    const bg = computed(() => (typeof props.bgVariant !== 'undefined' ? `bg-${props.bgVariant}` : null))
</script>

<template>
    <div :class="bg" class="card p-3">
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
