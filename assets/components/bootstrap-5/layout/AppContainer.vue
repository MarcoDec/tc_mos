<script lang="ts" setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({fluid: {required: false, type: Boolean}})
    const containerClass = computed(() => ({
        container: !props.fluid,
        'container-fluid': props.fluid
    }))
</script>

<template>
    <div :class="containerClass">
        <slot/>
    </div>
</template>
