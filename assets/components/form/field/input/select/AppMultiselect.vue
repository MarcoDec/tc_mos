<script setup>
    import Multiselect from '@vueform/multiselect'

    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: Array}
    })
    const emit = defineEmits(['update:modelValue'])

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <Multiselect
        :id="id"
        :disabled="disabled"
        :form="form"
        :model-value="modelValue"
        :options="field.options.options"
        :value-prop="field.options.valueProp"
        class="text-dark"
        label="text"
        mode="tags"
        searchable
        @update:model-value="input"/>
</template>

<style src="@vueform/multiselect/themes/default.css"/>
