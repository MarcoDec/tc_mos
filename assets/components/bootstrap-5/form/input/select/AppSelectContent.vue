<script setup>
    import AppSelectGroup from './AppSelectGroup.vue'
    import AppSelectOption from './AppSelectOption'
    import {computed} from 'vue'

    const props = defineProps({option: {required: true, type: Object}})
    const tag = computed(() => (Array.isArray(props.option.options) ? AppSelectGroup : AppSelectOption))
</script>

<template>
    <component :is="tag" :option="option"/>
</template>
