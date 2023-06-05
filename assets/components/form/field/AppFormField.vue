<script setup>
    import {computed, defineEmits, defineProps} from 'vue'
    const emit = defineEmits(['update:modelValue', 'input'])
    const props = defineProps({
        field: {required: true, type: Object},
        newField: {required: false, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: Object}
    })
    // console.log('newField',props.newField);
    // console.log('field',props.field);
    // const myFieled = computed(()=>props.newField?props.newField:props.field)
    // console.log('myFieled', myFieled);
    const td = computed(() => {
        switch (props.field.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return 'AppFormFieldSet'
            default:
                return 'AppFormGroupJS'
        }
    })
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="td"
        :field="field"
        :form="form"
        :model-value="modelValue"
        :new-field="newField"
        :values="modelValue"
        @update:model-value="input">
        <slot/>
    </component>
</template>
