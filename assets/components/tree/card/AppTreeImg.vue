<script setup>
    defineProps({tree: {required: true, type: Object}})
</script>

<template>
    <img v-if="tree.hasIcon" :src="tree.icon"/>
    <div v-else class="alert alert-primary">
        Aucune icône
    </div>
</template>
