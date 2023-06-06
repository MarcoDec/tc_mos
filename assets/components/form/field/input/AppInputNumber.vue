<script setup>
    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: 0, type: Number}
    })

    const emit = defineEmits(['update:modelValue'])

    function input(v) {
        emit('update:modelValue', parseFloat(v))
    }
</script>

<template>
    <AppInput
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :model-value="modelValue"
        @update:model-value="input"/>
</template>
