<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, ref, withDefaults} from 'vue'
    import clone from 'clone'

    const form = ref<HTMLFormElement>()
    const emit = defineEmits<{(e: 'update:values', values: FormValues): void,
                              (e: 'submit', data: FormData): void,
    }>()
    const props = withDefaults(
        defineProps<{fields: FormField[], values?: FormValues}>(),
        {values: () => ({})}
    )

    function input({name, value}: {value: FormValue, name: string}): void {
        const cloned = clone(props.values)
        cloned[name] = value
        emit('update:values', cloned)
    }
    function submit(): void{
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
            :value="values[field.name]"
            @input="input"/>
        <slot name="buttons"/>
    </form>
</template>
