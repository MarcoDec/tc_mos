<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = withDefaults(
        defineProps<{field: FormField, modelValue?: FormValue, size?: BootstrapSize}>(),
        {modelValue: null, size: 'sm'}
    )
    const sizeClass = computed(() => `btn-group-${props.size}`)

    const options = computed(() => props.field.options ?? [])

    function input(value: FormValue): void {
        emit('update:modelValue', value)
    }
</script>

<template>
    <div :class="sizeClass" class="btn-group">
        <AppRadio
            v-for="option in options"
            :id="field.id"
            :key="option.value"
            :field="field"
            :model-value="modelValue"
            :option="option"
            :size="size"
            @update:model-value="input"/>
    </div>
</template>
