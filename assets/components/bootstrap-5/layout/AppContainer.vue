<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import {isEmpty} from '../../../utils'

    const props = defineProps({cssClass: {default: null, type: String}, fluid: {type: Boolean}})
    const fluidClass = computed(() => (props.fluid ? 'container-fluid' : 'container'))
    const containerClass = computed(() => {
        let fullClasses = fluidClass.value
        if (!isEmpty(props.cssClass))
            fullClasses += ` ${props.cssClass}`
        return fullClasses
    })
</script>

<template>
    <div :class="containerClass">
        <slot/>
    </div>
</template>
