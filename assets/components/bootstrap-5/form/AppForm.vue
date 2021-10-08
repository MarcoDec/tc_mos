<script lang="ts" setup>
    import {findFields, registerForm} from '../../../store/bootstrap-5/Form'
    import {Field} from '../../../store/bootstrap-5/Field'
    import type {PropType} from 'vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        fields: {
            required: true,
            type: Array as PropType<Field[]>,
            validator: (value: unknown[]): boolean => value.every(field => field instanceof Field)
        },
        id: {required: true, type: String as PropType<string>}
    })

    registerForm(props.id, props.fields)

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const storedFields = findFields(props.id)
</script>

<template>
    <form :id="id"/>
</template>
