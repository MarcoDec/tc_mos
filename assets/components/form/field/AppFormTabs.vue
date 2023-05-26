<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppFormField from './AppFormField.vue'
    // import AppTab from '../../tab/AppTab.vue'
    // import AppTabs from '../../tab/AppTabs.vue'
    import AppTab from '../../tabs/AppTab.vue'
    import AppTabs from '../../tabs/AppTabs.vue'

    const emit = defineEmits(['update:modelValue'])

    const props = defineProps({
        field: {required: true, type: Object},
        newField: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null}
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
                    :new-field="newField"
                    :key="child"
                    :field="child"
                    :form="form"
                    :model-value="modelValue"
                    @update:model-value="input"/>
            </slot>
        </AppTab>
    </AppTabs>
</template>
