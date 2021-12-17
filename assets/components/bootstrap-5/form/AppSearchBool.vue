<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: FormField, id: string, modelValue?: FormValue}>()
    const radioGroup = computed<FormField>(() => ({
        btn: true,
        label: props.field.label,
        name: props.field.name,
        options: [{text: '', value: null}, {text: 'Oui', value: true}, {text: 'Non', value: false}],
        type: 'radio'
    }))
</script>

<template>
    <AppInputGuesser :id="id" :field="radioGroup" :model-value="modelValue"/>
</template>
