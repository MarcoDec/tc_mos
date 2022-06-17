<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {PropType} from 'vue'

    const props = defineProps({
        cssClass: {default: null, type: String as PropType<string | null>},
        fluid: {type: Boolean}
    })
    const fluidClass = computed(() => (props.fluid ? 'container-fluid' : 'container'))
    const containerClass = computed(() => {
        let fullClasses = fluidClass.value
        if (typeof props.cssClass !== 'undefined' && props.cssClass !== null && props.cssClass.length > 0)
            fullClasses += ` ${props.cssClass}`
        return fullClasses
    })
</script>

<template>
    <div :class="containerClass">
        <slot/>
    </div>
</template>
