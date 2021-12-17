<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    defineProps<{field: FormField, id: string, modelValue?: FormValue}>()

    function input(e: InputEvent): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
</script>

<template>
    <div class="form-check form-switch">
        <input :id="id" :name="field.name" :value="modelValue" class="form-check-input" type="checkbox" @input="input"/>
        <label :for="id" class="form-check-label">{{ field.label }}</label>
    </div>
</template>
