<script setup>
    import AppOption from './AppOption.vue'

    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: String}
    })
    const emit = defineEmits(['update:modelValue'])

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <select
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :value="modelValue"
        class="form-select form-select-sm"
        @input="input">
        <AppOption v-for="option in field.options" :key="option.value" :option="option"/>
    </select>
</template>
