<script setup>
    import {computed} from 'vue-demi'

    const emit = defineEmits(['update:modelValue'])

    const fields = computed(() => [
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

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <AppForm id="employee" :fields="fields" @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
