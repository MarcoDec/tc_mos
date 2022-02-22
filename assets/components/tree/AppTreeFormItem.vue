<script lang="ts" setup>
    import type {ComputedRef, Ref} from 'vue'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject, provide} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'

    const props = defineProps<{id: string, selected: string}>()
    const fields = inject<ComputedRef<FormField[]>>('fields', computed(() => []))
    const label = useNamespacedGetters(props.selected, ['label']).label as Ref<string>
    const title = computed(() => `Modification de ${label.value}`)
    const parentModuleName = useNamespacedState(props.selected, ['parentModuleName']).parentModuleName as Ref<string>
    const state = useNamespacedState<FormValues>(props.selected, fields.value.map(({name}) => name))
    const stateValues = computed(() => {
        const values: FormValues = {}
        for (const property in state)
            values[property] = state[property].value
        return values
    })
    const violations = useNamespacedState(props.selected, ['violations']).violations
    const unselect = useNamespacedActions(parentModuleName.value, ['unselect']).unselect
    const {remove, update} = useNamespacedActions(props.selected, ['remove', 'update'])

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
