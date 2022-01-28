<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps, withDefaults} from 'vue'

    const props = withDefaults(
        defineProps<{field: FormField, modelValue?: FormValue, size?: BootstrapSize, disabled: boolean}>(),
        {modelValue: null, size: 'sm'}
    )
    const options = computed(() => props.field.options ?? [])
    const sizeClass = computed(() => `form-select-${props.size}`)
</script>

<template>
    <select :id="field.id" :class="sizeClass" :name="field.name" :value="modelValue" class="form-select">
        <AppSelectOption v-for="option in options" :key="option.value" :option="option" :disabled="disabled"/>
    </select>
</template>
