<script lang="ts" setup>
    import type {ComputedRef, Ref} from 'vue'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, inject} from 'vue'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update', data: FormData) => void>()
    const props = defineProps<{id: string, selected: string}>()
    const fields = inject<ComputedRef<FormField[]>>('fields', computed(() => []))
    const label = useNamespacedGetters(props.selected, ['label']).label as Ref<string>
    const title = computed(() => `Modification de ${label.value}`)
    const state = useNamespacedState<FormValues>(props.selected, fields.value.map(({name}) => name))
    const stateValues = computed(() => {
        const values: FormValues = {}
        for (const property in state)
            values[property] = state[property].value
        return values
    })

    function submit(data: FormData): void {
        emit('update', data)
    }
</script>

<template>
    <AppTreeForm :id="id" :state="stateValues" :title="title" @create="submit">
        <AppBtn class="me-2" type="submit" variant="success">
            <Fa icon="pencil-alt"/>
            Modifier
        </AppBtn>
        <AppBtn variant="danger">
            <Fa icon="trash"/>
            Supprimer
        </AppBtn>
    </AppTreeForm>
</template>
