<script setup>
    import {defineProps, defineEmits} from 'vue';

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: [Object, String]},
        newField: {default: null, required: false, type: Object}
    });

    const emit = defineEmits(['update:modelValue', 'input']);

    function input(value) {
        emit('update:modelValue', value);
    }

    function getComponent(child) {
        switch (child.mode) {
        case 'tab':
            return 'AppFormTabs';
        case 'fieldset':
            return 'AppFormFieldset';
        default:
            return 'AppFormGroupJS';
        }
    }
</script>
<template>
    <div class="flex-wrapper">
        <component
            v-for="child in field.children"
            :key="child.name"
            :is="getComponent(child)"
            :field="child"
            :form="form"
            :name="child.name"
            :model-value="modelValue"
            :new-field="newField"
            :values="modelValue"
            @update:model-value="input"
        >
            <slot/>
        </component>
    </div>
</template>

<style scoped>
.flex-wrapper {
    display: flex;
    flex-wrap: wrap;
}
</style>
