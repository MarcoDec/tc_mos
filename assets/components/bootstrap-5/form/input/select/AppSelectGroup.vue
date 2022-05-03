<script setup>
    import AppSelectContent from './AppSelectContent.vue'

    defineProps({option: {required: true, type: Object}})
</script>

<template>
    <optgroup :label="option.text">
        <AppSelectContent v-for="child in option.options" :key="child.value" :option="child"/>
    </optgroup>
</template>
