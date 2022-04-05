<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../../types/bootstrap-5'

    const props = withDefaults(
        defineProps<{bgVariant?: BootstrapVariant | null, titleTag?: string, title: string}>(),
        {bgVariant: null, titleTag: 'h2'}
    )
    const bg = computed(() => (props.bgVariant === null ? null : `bg-${props.bgVariant}`))
</script>

<template>
    <div :class="bg" class="card">
        <component :is="titleTag" class="card-title">
            {{ title }}
        </component>
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
