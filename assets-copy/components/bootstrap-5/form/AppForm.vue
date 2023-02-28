<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, withDefaults} from 'vue'
    import clone from 'clone'

    const form = ref<HTMLFormElement>()
    const emit = defineEmits<{(e: 'update:values', values: Readonly<FormValues>): void, (e: 'submit'): void,
       (e: 'submit', values: FormData): void,
       (e: 'update:modelValue', values: FormValues): void}>()
    const props = withDefaults(
        defineProps<{fields: FormField[], values?: FormValues}>(),
        {values: () => ({})}
    )

    function input(value: Readonly<{value: FormValue, name: string}>): void {
        const cloned = clone(props.values)
        cloned[value.name] = value.value
        emit('update:values', cloned)
    }
</script>

<template>
    <form autocomplete="off" @submit.prevent="emit('submit')">
        <AppFormGroup
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :value="values[field.name]"
            @input="input"/>
        <slot name="buttons"/>
    </form>
</template>
