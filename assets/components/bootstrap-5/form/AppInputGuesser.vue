<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = defineProps<{field: FormField, id: string, modelValue?: FormValue}>()
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

    function input(value: FormValue): void {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component :is="guessed" :id="id" :field="field" :model-value="modelValue" @update:model-value="input"/>
</template>
