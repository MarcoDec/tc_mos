<script lang="ts" setup>
    import type {FormField, FormValue, InputValue} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps} from 'vue'

    const emit = defineEmits<{
        (e: 'input', payload: InputValue): void
        (e: 'update:modelValue', value: FormValue): void
    }>()
    const props = defineProps<{field: FormField, modelValue?: FormValue}>()

    function input(value: FormValue): void {
        emit('input', {name: props.field.name, value})
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel>
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInput :field="field" :model-value="modelValue" @update:model-value="input"/>
        </AppCol>
    </AppRow>
</template>
