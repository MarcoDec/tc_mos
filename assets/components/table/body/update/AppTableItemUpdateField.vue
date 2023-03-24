<script setup>
    import AppTableFormField from './../../AppTableFormField.vue'
    import AppTableItemField from './../read/AppTableItemField.vue'
    defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        modelValue: {default: () => ({}), type: Object},
        row: {required: true, type: String},
        violations: {default: () => [], type: Array}
    })
    const emit = defineEmits(['update:modelValue'])

    function input(v) {
        emit('update:modelValue', v)
    }
</script>

<template>
    <AppTableFormField
        v-if="field.update"
        :field="field"
        :form="form"
        :model-value="modelValue"
        :store="item"
        :violations="violations"
        @update:model-value="input"/>
    <AppTableItemField v-else :field="field" :item="item" :row="row"/>
</template>
