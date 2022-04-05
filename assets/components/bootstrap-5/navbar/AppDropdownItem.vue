<script setup>
    import {computed} from 'vue'

    const props = defineProps({variant: {default: 'dark', type: String}})
    const bg = computed(() => `bg-${props.variant}`)
</script>

<template>
    <li :class="bg" class="dropdown-item text-dark">
        <slot/>
    </li>
</template>
