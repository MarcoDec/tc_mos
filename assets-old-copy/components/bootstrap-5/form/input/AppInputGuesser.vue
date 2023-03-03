<script setup>
    import AppInput from './AppInput.vue'
    import AppSelect from './AppSelect.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const type = computed(() => props.field.type ?? 'text')
    const guessed = computed(() => {
        switch (type.value) {
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
        :field="field"
        :model-value="modelValue"
        :size="size"
        @update:model-value="input"/>
</template>
