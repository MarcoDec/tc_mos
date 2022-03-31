<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null, type: [Boolean, Number, String]},
        noLabel: {required: false, type: Boolean},
        size: {default: 'sm', type: String}
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
            default:
                return 'AppInput'
        }
    })

    function input(value) {
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
