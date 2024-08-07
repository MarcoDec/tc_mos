<script setup>
    import AppOptionGroup from './AppOptionGroup.vue'

    defineProps({groups: {required: true, type: Array}})
</script>

<template>
    <AppOptionGroup v-for="group in groups" :key="group.label" :group="group"/>
</template>
