<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        label: {required: true, type: String},
        type: {default: 'button', type: String},
        variant: {default: 'primary', type: String}
    })
    const css = computed(() => `btn-${props.variant}`)
</script>

<template>
    <button :class="css" :type="type" class="btn btn-sm">
        {{ label }}
    </button>
</template>
