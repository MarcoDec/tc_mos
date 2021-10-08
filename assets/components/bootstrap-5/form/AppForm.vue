<script lang="ts" setup>
    import {computed, defineProps, onBeforeMount} from 'vue'
    import {hasForm, registerForm} from '../../../store/repository/bootstrap-5/form/FormRepository'
    import Field from '../../../store/entity/bootstrap-5/form/Field'
    import type {PropType} from 'vue'
    import {v4 as uuid} from 'uuid'

    const props = defineProps({
        fields: {
            required: true,
            type: Array as PropType<Field[]>,
            validator(fields: unknown[]): boolean {
                return fields.every(field => field instanceof Field)
            }
        },
        id: {default: '', type: String}
    })
    const safeId = computed(() => (props.id.length > 0 ? props.id : uuid()))

    onBeforeMount(() => {
        if (!hasForm(safeId.value).value)
            registerForm(safeId.value, props.fields)
    })
</script>

<template>
    <form :id="safeId">
        <slot/>
    </form>
</template>
