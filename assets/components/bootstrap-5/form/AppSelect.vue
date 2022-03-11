<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const props = withDefaults(
        defineProps<{field: FormField, modelValue?: FormValue, size?: BootstrapSize}>(),
        {modelValue: null, size: 'sm'}
    )
    const options = computed(() => props.field.options ?? [])
    const sizeClass = computed(() => `form-select-${props.size}`)
    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
        console.log('je suis ici',(e.target as HTMLInputElement).value);
    }
</script>

<template>
    <select :id="field.id" :class="sizeClass" :name="field.name" :value="modelValue" @input="input" class="form-select">
        <AppSelectOption v-for="option in options" :key="option.value" :option="option"/>
    </select>
</template>
