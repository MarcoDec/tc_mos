<script setup>
    import AppOption from './AppOption.vue'
    defineProps({options: {required: true, type: [Array, Object]}})
</script>

<template>
    <AppOption v-for="option in options" :key="option.value" :option="option"/>
</template>
