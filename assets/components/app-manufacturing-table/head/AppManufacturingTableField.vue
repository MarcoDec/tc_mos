<script setup>
    import {defineProps} from 'vue'
    defineProps({field: {required: true, type: Object}})
</script>

<template>
    <th>
        {{ field.label }}
    </th>
</template>

<style scoped>
    th {
        position: sticky;
        top: 0;
        z-index: 1;
    }
</style>
