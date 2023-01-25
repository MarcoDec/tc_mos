<script setup>
    defineProps({group: {required: true, type: Object}})
</script>

<template>
    <optgroup :label="group.label">
        <AppOptions :options="group.options"/>
    </optgroup>
</template>
