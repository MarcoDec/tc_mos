<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        id: {required: true, type: String},
        modelValue: {default: null}
    })
    const radioGroup = computed(() => ({
        btn: true,
        label: props.field.label,
        name: props.field.name,
        options: [{text: '', value: null}, {text: 'Oui', value: 'true'}, {text: 'Non', value: 'false'}],
        type: 'radio'
    }))

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppInputGuesser :id="id" :field="radioGroup" :model-value="modelValue" @update:model-value="input"/>
</template>
