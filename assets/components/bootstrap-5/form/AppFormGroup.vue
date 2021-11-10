
<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{field: FormField, value?: number | string}>()
    const emit = defineEmits<{
        (e: 'update:value', value: number | string): void
        (e: 'input', payload: {value: number | string, name: string}): void
    }>()

    function input(value: number | string): void {
        emit('update:value', value)
        emit('input', {value, name: props.field.name})
    }
</script>

<template>
    <AppRow css-class="mb-3">
        <AppLabel>
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInput :field="field" :value="value" @update:value="input"/>
        </AppCol>
    </AppRow>
</template>
