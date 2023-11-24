<script setup>
    defineProps({option: {required: true, type: Object}})
</script>

<template>
    <option :value="option.value">
        {{ option.text }}
    </option>
</template>
