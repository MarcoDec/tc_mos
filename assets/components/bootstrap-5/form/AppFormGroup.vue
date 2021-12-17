<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps} from 'vue'

    const emit = defineEmits<{
        (e: 'update:value', value: FormValue): void
        (e: 'input', payload: Readonly<{value: FormValue, name: string}>): void
    }>()
    const props = defineProps<{field: FormField, value?: FormValue}>()

    function input(value: FormValue): void {
        emit('update:value', value)
        emit('input', {name: props.field.name, value})
    }
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel>
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInputGuesser :field="field" :value="value" @update:value="input"/>
        </AppCol>
    </AppRow>
</template>
