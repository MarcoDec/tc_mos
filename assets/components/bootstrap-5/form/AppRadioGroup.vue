<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = defineProps<{field: FormField, id: string, modelValue?: FormValue}>()

    const options = computed(() => props.field.options ?? [])

    function input(value: FormValue): void {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppRadio
        v-for="option in options"
        :id="id"
        :field="field"
        :model-value="modelValue"
        :option="option"
        @update:model-value="input"/>
</template>
