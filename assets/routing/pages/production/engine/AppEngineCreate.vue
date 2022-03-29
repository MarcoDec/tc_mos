<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/engines/groupes'
    import type {
        Actions as ActionsCompagnie,
        Getters as GettersCompagnie
    } from '../../../../store/engines/compagnies'
    import type {FormField, FormValue} from '../../../../types/bootstrap-5'
    import {computed, defineEmits, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const fetchGroupe = useNamespacedActions<Actions>('groupes', [
        'fetchGroupe'
    ]).fetchGroupe
    const fetchCompagnie = useNamespacedActions<ActionsCompagnie>('compagnies', [
        'fetchCompagnie'
    ]).fetchCompagnie

    const options = useNamespacedGetters<Getters>('groupes', ['options']).options
    const optionsCompagnie = useNamespacedGetters<GettersCompagnie>('compagnies', [
        'options'
    ]).options

    const fields = computed<FormField[]>(() => [
        {
            label: 'Groupe d\'équipement',
            name: 'groupe',
            options: options.value,
            type: 'select'
        },

        {
            label: 'Compagnie',
            name: 'compagnie',
            options: optionsCompagnie.value,
            type: 'select'
        },
        {label: 'Nom', name: 'nom '},
        {label: 'Propriétaire', name: 'proprietaire'}
    ])

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
    onMounted(async () => {
        await fetchGroupe()
        await fetchCompagnie()
    })
</script>

<template>
    <AppForm id="login" :fields="fields" country-field="pays" @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.overflow-auto {
  overflow: initial !important;
}
</style>
