<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const fields = computed<FormField[]>(() => [
        {label: 'Prenom ', name: 'prenom'},
        {label: 'Nom', name: 'nom'},
        {label: 'Initiales', name: 'initiales'},
        {
            label: 'Niveau ',
            name: 'niveau',
            options: [
                {text: 'Opérateur', value: '1'},
                {text: 'Animateur', value: '2'},
                {text: 'Responsable', value: '3'},
                {text: 'Directeur', value: '4'}
            ],
            type: 'radio'
        },
        {label: 'Compte utilisateur', name: 'compte', type: 'boolean'}
    ])

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
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
