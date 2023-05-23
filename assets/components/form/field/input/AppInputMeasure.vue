<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: Object}
    })
    const codeId = computed(() => `${props.id}-code`)
    const valueId = computed(() => `${props.id}-value`)

    function codeValue(code) {
        emit('update:modelValue', {...props.modelValue, code})
    }

    function inputValue(value) {
        emit('update:modelValue', {...props.modelValue, value})
    }
</script>

<template>
    <div :id="id" class="input-group">
        <AppInputGuesser
            :id="valueId"
            :disabled="disabled"
            :field="field.measure.value"
            :form="form"
            :model-value="modelValue?.value"
            @update:model-value="inputValue"/>
        <AppInputGuesser
            :id="codeId"
            :disabled="disabled"
            :field="field.measure.code"
            :form="form"
            :model-value="modelValue?.code"
            @update:model-value="codeValue"/>
    </div>
</template>
