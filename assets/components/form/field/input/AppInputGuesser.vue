<script setup>
    /* eslint-disable vue/no-unused-properties */
    import AppInput from './AppInput.vue'
    import AppInputMeasure from './AppInputMeasure.vue'
    import AppInputNumber from './AppInputNumber.vue'
    import AppMultiselect from './select/AppMultiselect.vue'
    import AppSelect from './select/AppSelect.vue'
    import AppSwitch from './AppSwitch.vue'
    import {computed} from 'vue'
    import AppTextArea from './AppTextArea.vue'
    import AppMultiselectFetch from './select/AppMultiselectFetch.vue'

    const emit = defineEmits(['update:modelValue', 'searchChange'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    const kind = computed(() => {
        switch (props.field.type) {
            case 'boolean':
                return AppSwitch
            case 'number':
                return AppInputNumber
            case 'measure':
                return AppInputMeasure
            case 'multiselect':
                return AppMultiselect
            case 'multiselect-fetch':
                return AppMultiselectFetch
            case 'select':
                return AppSelect
            case 'textarea':
                return AppTextArea
            default:
                return AppInput
        }
    })

    function input(v) {
        emit('update:modelValue', v)
    }
    function searchChange(data) {
        emit('searchChange', {field: props.field, data})
    }
</script>

<template>
    <component :is="kind" v-bind="$props" @update:model-value="input" @search-change="searchChange"/>
</template>
