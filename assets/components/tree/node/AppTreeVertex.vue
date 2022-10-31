<script setup>
    defineProps({node: {required: true, type: Object}})
</script>

<template>
    <AppTreeLabel :node="node">
        <Fa :icon="node.icon" class="me-1"/>
    </AppTreeLabel>
</template>
