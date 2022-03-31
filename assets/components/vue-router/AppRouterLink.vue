<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        disabled: {required: false, type: Boolean},
        to: {required: true, type: String}
    })
    const link = computed(() => ({name: props.to}))
</script>

<template>
    <span v-if="disabled">
        <slot/>
        <Fa icon="triangle-exclamation"/>
    </span>
    <RouterLink v-else :to="link">
        <slot/>
    </RouterLink>
</template>
