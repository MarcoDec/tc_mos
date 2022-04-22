<script setup>
    import AppInput from './AppInput.vue'
    import AppRadioGroup from './AppRadioGroup.vue'
    import AppSearchBool from './AppSearchBool.vue'
    import AppSelect from './AppSelect.vue'
    import AppSwitch from './AppSwitch.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        id: {required: true, type: String},
        method: {default: null, type: String},
        modelValue: {default: null},
        noLabel: {required: false, type: Boolean},
        size: {default: 'sm', type: String}
    })
    const type = computed(() => props.field.type ?? 'text')
    const guessed = computed(() => {
        switch (type.value) {
            case 'boolean':
                return AppSwitch
            case 'radio':
                return AppRadioGroup
            case 'search-boolean':
                return AppSearchBool
            case 'select':
                return AppSelect
            default:
                return AppInput
        }
    })

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="guessed"
        :id="id"
        :disabled="disabled"
        :field="field"
        :method="method"
        :model-value="modelValue"
        :no-label="noLabel"
        :size="size"
        @update:model-value="input"/>
</template>
