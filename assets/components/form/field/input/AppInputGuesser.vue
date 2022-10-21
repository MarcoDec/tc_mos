<script setup>
    /* eslint-disable vue/no-unused-properties */
    import {computed, defineAsyncComponent} from 'vue'
    import AppInput from './AppInput.vue'
    import AppInputMeasure from './AppInputMeasure.vue'
    import AppSelect from './select/AppSelect.vue'
    import AppSwitch from './AppSwitch.vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: [Boolean, Number, String, Object]}
    })
    const type = computed(() => {
        switch (props.field.type) {
            case 'boolean':
                return AppSwitch
            case 'measure':
                return AppInputMeasure
            case 'multiselect':
                return defineAsyncComponent(() => import('./select/AppMultiselect.vue'))
            case 'select':
                return AppSelect
            default:
                return AppInput
        }
    })

    function input(v) {
        emit('update:modelValue', v)
    }
</script>

<template>
    <component :is="type" v-bind="$props" @update:model-value="input"/>
</template>
