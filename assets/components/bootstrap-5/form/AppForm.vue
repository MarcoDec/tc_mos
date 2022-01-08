<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, ref, withDefaults} from 'vue'
    import clone from 'clone'

    const form = ref<HTMLFormElement>()
    const emit = defineEmits<{
        (e: 'submit', data: FormData): void
        (e: 'update:modelValue', values: FormValues): void
    }>()
    const props = withDefaults(
        defineProps<{fields: FormField[], modelValue?: FormValues}>(),
        {modelValue: () => ({})}
    )

    function input({name, value}: {value: FormValue, name: string}): void {
        const cloned = clone(props.modelValue)
        cloned[name] = value
        emit('update:modelValue', cloned)
    }

    function submit(): void {
        if (typeof form.value !== 'undefined')
            emit('submit', new FormData(form.value))
    }
</script>

<template>
    <form ref="form" autocomplete="off" @submit.prevent="submit">
        <AppFormGroup
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :model-value="modelValue[field.name]"
            @input="input"/>
        <div class="float-end">
            <slot/>
        </div>
    </form>
</template>
