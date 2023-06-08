<script setup>
    defineProps({field: {required: true, type: Object}})
</script>

<template>
    <th>{{ field.label }}</th>
</template>
