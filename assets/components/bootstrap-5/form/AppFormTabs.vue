<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    defineProps<{
        field: FormField
        form: string
        modelValue?: FormValue
    }>()

    function input(value: FormValue): void {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppTab
        :id="field.name"
        :icon="field.icon"
        :title="field.label"
        :active="field.active">
        <slot>
            <AppFormField
                v-for="child in field.children"
                :key="child"
                :field="child"
                :form="form"
                :model-value="modelValue"
                @update:model-value="input"/>
        </slot>
    </AppTab>
</template>
