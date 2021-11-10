<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import AppFormGroup from './AppFormGroup.vue'
    import type {FormField} from '../../../types/bootstrap-5'
    import clone from 'clone'

    const props = defineProps<{fields: FormField[], values?: Record<string, number | string>}>()
    const formData = computed(() => (typeof props.values !== 'undefined' ? props.values : {}))
    const emit = defineEmits<{
        (e: 'update:values', values: Record<string, number | string>): void
        (e: 'submit'): void
    }>()

    function input({name, value}: {value: number | string, name: string}): void{
        const cloned = clone(formData.value)
        cloned[name] = value
        emit('update:values', cloned)

    }
</script>


<template>
    <form autocomplete="off" @submit.prevent="emit('submit')">
        <AppFormGroup v-for="field in fields" :key="field.name" :field="field" @input="input"/>
        <AppBtn class="float-end" type="submit">
            Connexion
        </AppBtn>
    </form>
</template>
