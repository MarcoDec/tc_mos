<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: FormField, modelValue?: FormValue}>()
    const radioGroup = computed<FormField>(() => ({
        btn: true,
        id: props.field.id,
        label: props.field.label,
        name: props.field.name,
        options: [{text: '', value: null}, {text: 'Oui', value: true}, {text: 'Non', value: false}],
        type: 'radio'
    }))
</script>

<template>
    <AppInputGuesser :field="radioGroup" :model-value="modelValue"/>
</template>
