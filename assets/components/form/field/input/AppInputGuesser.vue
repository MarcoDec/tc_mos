<script setup>
    /* eslint-disable vue/no-unused-properties */
    import AppInputMeasure from './AppInputMeasure.vue'
    import AppInputNumber from './AppInputNumber.vue'
    import AppSelect from './select/AppSelect.vue'
    import AppMultiselect from './select/AppMultiselect.vue'
    import AppSwitch from './AppSwitch.vue'
    import {computed} from 'vue'
    import AppTextArea from './AppTextArea.vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    const type = computed(() => {
        //console.log(props.field)
        switch (props.field.type) {
            case 'boolean':
                //console.log(props.field)
                return AppSwitch
            case 'number':
                return AppInputNumber
            case 'measure':
                return AppInputMeasure
            case 'multiselect':
                //console.log('multiselect')
                return AppMultiselect
            case 'select':
                return AppSelect
            case 'textarea':
                return AppTextArea
            default:
                return 'AppInput'
        }
    })

    function input(v) {
        emit('update:modelValue', v)
    }
</script>

<template>
    <component :is="type" v-bind="$props" @update:model-value="input"/>
</template>
