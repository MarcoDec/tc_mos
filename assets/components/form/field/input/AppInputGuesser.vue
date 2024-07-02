<script setup>
    import AppInput from './AppInput.vue'
    import AppInputMeasure from './AppInputMeasure.vue'
    import AppInputNumber from './AppInputNumber.vue'
    import AppTrafficLight from './AppTrafficLight.vue'
    import AppMultiselect from './select/AppMultiselect.vue'
    import AppSelect from './select/AppSelect.vue'
    import AppSwitch from './AppSwitch.vue'
    import {computed} from 'vue'
    import AppTextArea from './AppTextArea.vue'
    import AppMultiselectFetch from './select/AppMultiselectFetch.vue'

    const emit = defineEmits(['update:modelValue', 'searchChange', 'focusOut'])
    // on échappe eslint pour les no-unused-properties
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    console.log('AppInputGuesser.vue', props)
    const kind = computed(() => {
        switch (props.field.type) {
            case 'boolean':
                return AppSwitch
            case 'number' || 'int':
                return AppInputNumber
            case 'measure':
                return AppInputMeasure
            case 'multiselect':
                return AppMultiselect
            case 'multiselect-fetch':
                return AppMultiselectFetch
            case 'select':
                return AppSelect
            case 'trafficLight':
                return AppTrafficLight
            case 'textarea':
                return AppTextArea
            default:
                return AppInput
        }
    })

    function input(v) {
        // console.log('input', v)
        emit('update:modelValue', v)
    }
    function searchChange(data) {
        emit('searchChange', {field: props.field, data})
    }
    function onFocusOut() {
        emit('focusOut', props.field.name)
    }
</script>

<template>
    <component
        :is="kind"
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :model-value="modelValue"
        @update:model-value="input"
        @focusout="onFocusOut"
        @search-change="searchChange"/>
</template>
