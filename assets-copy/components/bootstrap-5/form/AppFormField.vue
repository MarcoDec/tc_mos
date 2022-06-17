<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'

    const emit = defineEmits<{
        (e: 'update:modelValue', value: FormValue): void
        (e: 'input', payload: {value: FormValue, name: string}): void
    }>()
    const props
        = defineProps<{field: FormField, form: string, modelValue?: FormValue}>()

    const td = computed(() => {
        switch (props.field.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return 'AppFormFieldset'
            default:
                return 'AppFormGroup'
        }
    })

    function input(value: FormValue): void {
        emit('update:modelValue', value)
        emit('input', {name: props.field.name, value})
    }
</script>

<template>
    <component
        :is="td"
        :field="field"
        :form="form"
        :model-value="modelValue"
        @update:model-value="input">
        <slot/>
    </component>
</template>
