<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, provide, withDefaults} from 'vue'
    import clone from 'clone'

    const emit = defineEmits<{(e: 'update:modelValue', values: Readonly<FormValues>): void, (e: 'submit'): void}>()
    const props = withDefaults(
        defineProps<{countryField?: string | null, fields: FormField[], id: string, modelValue?: FormValues}>(),
        {countryField: null, modelValue: () => ({})}
    )
    const country = computed(() => (props.countryField !== null ? props.modelValue[props.countryField] : null))
    provide('country', country)

    function input(value: Readonly<{value: FormValue, name: string}>): void {
        const cloned = clone(props.modelValue)
        cloned[value.name] = value.value
        emit('update:modelValue', cloned)
    }
</script>

<template>
    <form :id="id" autocomplete="off" @submit.prevent="emit('submit')">
        <AppFormGroup
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :form="id"
            :model-value="modelValue[field.name]"
            @input="input"/>
        <div class="float-start">
            <slot name="start"/>
        </div>
        <div class="float-end">
            <slot/>
        </div>
    </form>
</template>
