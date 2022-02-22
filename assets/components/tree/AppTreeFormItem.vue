<script lang="ts" setup>
    import type {Actions, Getters, State} from '../../store/tree/item'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject, provide} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {ComputedRef} from 'vue'
    import type {Actions as TreeActions} from '../../store/tree'

    const fields = inject<ComputedRef<FormField[]>>('fields', computed(() => []))
    const modulePath = inject('modulePath', '')
    const props = defineProps<{id: string, selected: string}>()
    const label = useNamespacedGetters<Getters>(props.selected, ['label']).label
    const state = useNamespacedState<FormValues>(props.selected, fields.value.map(({name}) => name))
    const title = computed(() => `Modification de ${label.value}`)
    const stateValues = computed(() => {
        const values: FormValues = {}
        for (const property in state)
            values[property] = state[property].value
        return values
    })
    const violations = useNamespacedState<State>(props.selected, ['violations']).violations
    const unselect = useNamespacedActions<TreeActions>(modulePath, ['unselect']).unselect
    const {remove, update} = useNamespacedActions<Actions>(props.selected, ['remove', 'update'])

    provide('violations', violations)
</script>

<template>
    <AppTreeForm :id="id" :state="stateValues" :title="title" update @submit="update">
        <template #start>
            <AppBtn variant="danger" @click="unselect">
                <Fa icon="backward"/>
            </AppBtn>
        </template>
        <AppBtn class="me-2" type="submit" variant="success">
            <Fa icon="pencil-alt"/>
            Modifier
        </AppBtn>
        <AppBtn variant="danger" @click="remove">
            <Fa icon="trash"/>
            Supprimer
        </AppBtn>
    </AppTreeForm>
</template>
