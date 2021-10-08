<script lang="ts" setup>
    import {computed, defineProps, onBeforeMount} from 'vue'
    import Field from '../../../store/entity/bootstrap-5/form/Field'
    import type {PropType} from 'vue'
    import formRepository from '../../../store/repository/bootstrap-5/form/FormRepository'

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

    let form = null

    onBeforeMount(() => {
        formRepository.createForm(props.id, props.fields)
        form = computed(() => formRepository.find(props.id))
    })
</script>

<template>
    {{ form?.module }}
    <form :id="id"/>
</template>
