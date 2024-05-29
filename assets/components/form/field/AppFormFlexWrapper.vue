<script setup>
import {defineProps, defineEmits, ref, computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: [Object, String]},
        newField: {default: null, required: false, type: Object},
        disabled: {default: false, type: Boolean}
    })
    const emit = defineEmits(['update:modelValue', 'input'])
    const localData = ref({})
    localData.value = props.modelValue
    function input(value, fieldName) {
        localData.value[fieldName] = value
        emit('update:modelValue', localData.value)
    }

    function getComponent(child) {
        switch (child.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return 'AppFormFieldset'
            default:
                return 'AppFormGroupJS'
        }
    }
    function getChildStyle() {
        return {
            width: props.field.wrapWidth || '100%',
            minWidth: props.field.wrapMinWidth || '400px',
            margin: '5px',
            fontSize: props.field.fontSize || '1rem'
        }
    }
    const isDisabled = computed(() => props.disabled || props.field.readOnly)
</script>

<template>
    <div class="flex-wrapper">
        <component
            :is="getComponent(child)"
            v-for="child in field.children"
            :key="child.name"
            class="flex-fill"
            :disabled="isDisabled"
            :field="child"
            :form="form"
            :name="child.name"
            :model-value="modelValue[child.name]"
            :new-field="newField"
            :style="getChildStyle()"
            :values="modelValue[child.name]"
            @update:model-value="(value) => input(value, child.name)">
            <slot/>
        </component>
    </div>
</template>

<style scoped>
    .flex-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
</style>
