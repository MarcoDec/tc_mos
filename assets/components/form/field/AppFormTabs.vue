<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppFormField from './AppFormField.vue'
    import AppTab from '../../tab/AppTab.vue'

    const emit = defineEmits(['update:modelValue'])

    defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null}
    })

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppTab
        :id="field.name"
        :icon="field.icon"
        :title="field.label"
        :active="field.active">
        <slot>
            <AppFormField
                v-for="child in field.children"
                :key="child"
                :field="child"
                :form="form"
                :model-value="modelValue"
                @update:model-value="input"/>
        </slot>
    </AppTab>
</template>
