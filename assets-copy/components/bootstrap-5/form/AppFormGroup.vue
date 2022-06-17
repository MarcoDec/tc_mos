<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'

    const emit = defineEmits<{
        (e: 'update:modelValue', value: FormValue): void
        (e: 'input', payload: Readonly<{value: FormValue, name: string}>): void
    }>()
    const props = defineProps<{field: FormField, form: string, modelValue?: FormValue, disabled: boolean}>()
    const inputFields = computed<FormField>(() => ({
        ...props.field,
        id: props.field.id ?? `${props.form}-${props.field.name}`
    }))

    function input(value: FormValue): void {
        emit('update:modelValue', value)
        emit('input', {name: props.field.name, value})
    }
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel>
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInputGuesser :disabled="disabled" :field="inputFields" :model-value="modelValue" @update:model-value="input"/>
        </AppCol>
    </AppRow>
</template>
