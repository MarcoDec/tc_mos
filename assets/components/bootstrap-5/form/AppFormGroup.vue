<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, inject, ref} from 'vue'
    import type {Ref} from 'vue'
    import type {Violation} from '../../../types/types'

    const emit = defineEmits<{
        (e: 'update:modelValue', value: FormValue): void
        (e: 'input', payload: {value: FormValue, name: string}): void
    }>()
    const props = defineProps<{field: FormField, form: string, modelValue?: FormValue}>()
    const inputFields = computed<FormField>(() => ({
        ...props.field,
        id: props.field.id ?? `${props.form}-${props.field.name}`
    }))
    const labelCols = computed(() => props.field.labelCols ?? 2)
    const violations = inject<Ref<Violation[]>>('violations', ref([]))
    const violation = computed(() => violations.value.find(({propertyPath}) => propertyPath === props.field.name) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))

    function input(value: FormValue): void {
        emit('update:modelValue', value)
        emit('input', {name: props.field.name, value})
    }
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel :cols="labelCols">
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInputGuesser
                :class="isInvalid"
                :field="inputFields"
                :model-value="modelValue"
                no-label
                @update:model-value="input"/>
            <AppInvalidFeedback v-if="violation !== null">
                {{ violation.message }}
            </AppInvalidFeedback>
        </AppCol>
    </AppRow>
</template>
