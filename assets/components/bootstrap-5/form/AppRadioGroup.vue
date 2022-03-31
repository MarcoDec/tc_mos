<script setup>
    import AppRadio from './AppRadio'
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const sizeClass = computed(() => `btn-group-${props.size}`)
    const options = computed(() => props.field.options ?? [])

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <div :class="sizeClass" class="btn-group">
        <AppRadio
            v-for="option in options"
            :id="field.id"
            :key="option.value"
            :field="field"
            :model-value="modelValue"
            :option="option"
            :size="size"
            @update:model-value="input"/>
    </div>
</template>
