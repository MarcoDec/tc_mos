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
    const valueName = computed(() => `${props.field.name}-value`)
    const codeName = computed(() => `${props.field.name}-code`)

    function codeValue(code) {
        emit('update:modelValue', {...props.modelValue, code})
    }

    function inputValue(value) {
        emit('update:modelValue', {...props.modelValue, value})
    }
</script>

<template>
    <div :id="id" class="input-group">
        <AppInputGuesserJS
            :id="valueId"
            :disabled="disabled"
            :field="field.measure.value"
            :form="form"
            :name="valueName"
            :model-value="modelValue?.value"
            @update:model-value="inputValue"/>
        <AppInputGuesserJS
            :id="codeId"
            :disabled="disabled"
            :field="field.measure.code"
            :form="form"
            :name="codeName"
            :model-value="modelValue?.code"
            @update:model-value="codeValue"/>
    </div>
</template>
