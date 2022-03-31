<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null}
    })
    const radioGroup = computed(() => ({
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
