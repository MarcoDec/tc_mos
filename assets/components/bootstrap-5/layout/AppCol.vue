<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {PropType} from 'vue'
    import {colValidator as validator} from '../../../composition/boostrap-5'

    const props = defineProps({
        cols: {default: null, type: Number as PropType<number | null>, validator},
        tag: {default: 'div', type: String}
    })
    const colClass = computed(() => (props.cols === null ? 'col' : `col-${props.cols}`))
</script>

<template>
    <component :is="tag" :class="colClass">
        <slot/>
    </component>
</template>
