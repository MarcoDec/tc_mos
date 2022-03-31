<script setup>
    import {computed, inject, provide} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import AppTreeForm from './AppTreeForm.vue'

    const fields = inject('fields', computed(() => []))
    const moduleName = inject('moduleName', '')
    const props = defineProps({
        id: {required: true, type: String},
        selected: {required: true, type: String}
    })
    const label = useNamespacedGetters(props.selected, ['label']).label
    const state = useNamespacedState(props.selected, fields.value.map(({name}) => name))
    const title = computed(() => `Modification de ${label.value}`)
    const stateValues = computed(() => {
        const values = {}
        for (const property in state)
            values[property] = state[property].value
        return values
    })
    const violations = useNamespacedState(props.selected, ['violations']).violations
    const unselect = useNamespacedActions(moduleName, ['unselect']).unselect
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
