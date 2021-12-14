<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {PropType} from 'vue'

    const props = defineProps({
        cols: {
            default: null,
            type: Number as PropType<number | null>,
            validator: (value: number): boolean => value >= 1 && value <= 12
        },
        tag: {default: 'div', type: String}
    })
    const colClass = computed(() => (props.cols !== null ? `col-${props.cols}` : 'col'))
</script>

<template>
    <component :is="tag" :class="colClass">
        <slot/>
    </component>
</template>
