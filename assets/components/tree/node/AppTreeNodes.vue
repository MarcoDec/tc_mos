<script setup>
    import AppTreeNode from './AppTreeNode.vue'

    defineProps({nodes: {required: true, type: Array}})
</script>

<template>
    <div>
        <AppTreeNode v-for="node in nodes" :key="node.id" :node="node"/>
    </div>
</template>
