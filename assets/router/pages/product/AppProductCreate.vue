<script setup>
    import {computed} from 'vue-demi'

    const emit = defineEmits(['update:modelValue'])

    const options = [
        {text: 'Prototype', value: '1'},
        {text: 'EI', value: '2'},
        {text: 'Série', value: '3'},
        {text: 'Piéce de rechange', value: '4'}
    ]
    const fields = computed(() => [
        {
            label: 'Famille de Produit ',
            name: 'famille'
        },
        {label: 'Réf', name: 'ref'},
        {label: 'Désignation ', name: 'desgniation'},

        {
            label: 'Type de Produit',
            name: 'type',
            options: {
                label: value =>
                    options.find(option => option.value === value)?.text ?? null,
                options
            },
            type: 'select'
        },
        {
            label: 'Client principal',
            name: 'client'
        },

        {label: 'Infos publiques', name: 'info'},
        {label: 'Volume annuel prévisionnel', name: 'volume'},
        {label: 'Conditionnement (type)', name: 'condition'},
        {label: 'Date d\'expiration ', name: 'date', type: 'date'}
    ])

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <AppForm id="product" :fields="fields" @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
