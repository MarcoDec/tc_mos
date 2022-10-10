<script setup>
    import AppLabel from './AppLabel.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['input', 'update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: String}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)

    function input(value) {
        emit('input', props.field, value)
        emit('update:modelValue', value)
    }
</script>

<template>
    <div class="mb-3 row">
        <AppLabel :field="field" :input="inputId"/>
        <AppInputGuesser
            :id="inputId"
            :disabled="disabled"
            :field="field"
            :form="form"
            :model-value="modelValue"
            class="col"
            @update:model-value="input"/>
    </div>
</template>
