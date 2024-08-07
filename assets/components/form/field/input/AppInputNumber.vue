<script setup>
    import AppInput from './AppInput.vue'
    import {ref} from 'vue'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: 0, type: Number}
    })
    const emit = defineEmits(['update:modelValue'])
    const localData = ref(props.modelValue)

    function updateModelValue(v) {
        localData.value = v
    }

    function onFocusOut(e) {
        e.preventDefault()
        e.stopPropagation()
        emit('update:modelValue', parseFloat(localData.value))
    }
</script>

<template>
    <AppInput
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :model-value="modelValue"
        @update:model-value="updateModelValue"
        @focusout="onFocusOut"/>
</template>

<style scoped>
    /* Chrome, Safari, Edge, Opera */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
