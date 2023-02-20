<script setup>
    import {computed} from 'vue'

    const props = defineProps({disabled: {type: Boolean}, variant: {default: 'success', type: String}})
    const bg = computed(() => `bg-${props.variant}`)
    const css = computed(() => ({disabled: props.disabled}))
</script>

<template>
    <li :class="bg" class="text-dark">
        <span :class="css" class="dropdown-item">
            <slot/>
        </span>
    </li>
</template>
