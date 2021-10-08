<script lang="ts" setup>
    import {defineProps, onBeforeMount} from 'vue'
    import {hasForm, registerForm} from '../../../store/repository/bootstrap-5/form/FormRepository'
    import Field from '../../../store/entity/bootstrap-5/form/Field'
    import type {PropType} from 'vue'

    const props = defineProps({
        fields: {
            required: true,
            type: Array as PropType<Field[]>,
            validator(fields: unknown[]): boolean {
                return fields.every(field => field instanceof Field)
            }
        },
        id: {required: true, type: String as PropType<string>}
    })

    onBeforeMount(() => {
        if (!hasForm(props.id).value)
            registerForm(props.id, props.fields)
    })
</script>

<template>
    <form :id="id">
        <AppFormGroup v-for="field in fields" :key="field.name" :field="field"/>
    </form>
</template>
