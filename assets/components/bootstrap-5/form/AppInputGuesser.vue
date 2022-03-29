<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'
    import type {PropType} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = defineProps({
        field: {required: true, type: Object as PropType<FormField>},
        modelValue: {default: null, type: [Boolean, Number, String] as PropType<FormValue>},
        noLabel: {required: false, type: Boolean},
        size: {default: 'sm', type: String as PropType<BootstrapSize>}
    })
    const type = computed(() => props.field.type ?? 'text')

    const guessed = computed(() => {
        switch (type.value) {
            case 'boolean':
                return 'AppSwitch'
            case 'radio':
                return 'AppRadioGroup'
            case 'search-boolean':
                return 'AppSearchBool'
            case 'select':
                return 'AppSelect'
            case 'phone':
                return 'AppPhoneFlag'
            default:
                return 'AppInput'
        }
    })

    function input(value: FormValue): void {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="guessed"
        :field="field"
        :model-value="modelValue"
        :no-label="noLabel"
        :size="size"
        @update:model-value="input"/>
</template>
