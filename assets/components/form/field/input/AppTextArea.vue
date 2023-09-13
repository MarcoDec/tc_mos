<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: '', type: [Number, String]}
    })
    const fieldType = computed(() => props.field.type ?? 'textarea')
    function handleInput(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <textarea
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :placeholder="field.label"
        :type="fieldType"
        :value="modelValue"
        autocomplete="off"
        class="form-control form-control-sm"
        @input="handleInput"/>
</template>
