<script setup>
    import AppSelectOption from './AppSelectOption.vue'
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const options = computed(() => props.field.options ?? [])
    const sizeClass = computed(() => `form-select-${props.size}`)
</script>

<template>
    <select :id="field.id" :class="sizeClass" :name="field.name" :value="modelValue" class="form-select">
        <AppSelectOption v-for="option in options" :key="option.value" :option="option"/>
    </select>
</template>
