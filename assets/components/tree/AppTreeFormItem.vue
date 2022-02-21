<script lang="ts" setup>
    import type {ComputedRef, Ref} from 'vue'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'

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
    const update = useNamespacedActions(props.selected, ['update']).update as (body: FormData) => Promise<void>
</script>

<template>
    <AppTreeForm :id="id" :state="stateValues" :title="title" update @submit="update">
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
