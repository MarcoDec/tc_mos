<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, ref, withDefaults} from 'vue'
    import clone from 'clone'

    const form = ref<HTMLFormElement>()
    const emit = defineEmits<{
        (e: 'submit', values: Readonly<FormData>): void
        (e: 'update:modelValue', values: Readonly<FormValues>): void
    }>()
    const props = withDefaults(
        defineProps<{fields: FormField[], id: string, modelValue?: FormValues}>(),
        {modelValue: () => ({})}
    )

    function input(value: Readonly<{value: FormValue, name: string}>): void {
        const cloned = clone(props.modelValue)
        cloned[value.name] = value.value
        emit('update:modelValue', cloned)
    }

    function submit(): void {
        if (typeof form.value !== 'undefined')
            emit('submit', new FormData(form.value))
    }
</script>

<template>
    <form :id="id" ref="form" autocomplete="off" @submit.prevent="submit">
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
