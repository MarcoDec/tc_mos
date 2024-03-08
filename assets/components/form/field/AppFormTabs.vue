<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppFormField from './AppFormField.vue'
    // import AppTab from '../../tab/AppTab.vue'
    // import AppTabs from '../../tab/AppTabs.vue'
    import AppTab from '../../tab/AppTab.vue'
    import AppTabs from '../../tab/AppTabs.vue'

    const emit = defineEmits(['update:modelValue'])

    defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null},
        newField: {required: true, type: Object}
    })
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppTabs v-if="field.mode === 'tab'" id="gui-start" class="gui-start-content">
        <AppTab
            :id="field.name"
            :icon="field.icon"
            :title="field.label"
            :active="field.active"
            :tabs="`gui-start-${field.name}`">
            <slot>
                <AppFormField
                    v-for="child in field.children"
                    :key="child"
                    :new-field="newField"
                    :field="child"
                    :form="form"
                    :model-value="modelValue"
                    @update:model-value="input"/>
            </slot>
        </AppTab>
    </AppTabs>
</template>
