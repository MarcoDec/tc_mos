<script setup>
    import {computed, inject, ref} from 'vue'

    const emit = defineEmits(['input', 'update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null}
    })
    const inputFields = computed(() => ({
        ...props.field,
        id: props.field.id ?? `${props.form}-${props.field.name}`
    }))
    const labelCols = computed(() => props.field.labelCols ?? 2)
    const violations = inject('violations', ref([]))
    const violation = computed(() => violations.value.find(({propertyPath}) => propertyPath === props.field.name) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))

    function input(value) {
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
