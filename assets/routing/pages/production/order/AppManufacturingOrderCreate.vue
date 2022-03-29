<script lang="ts" setup>
    import type {
        Actions as ActionsCompagnie,
        Getters as GettersCompagnie
    } from '../../../../store/engines/compagnies'
    import type {
        Actions as ActionsProduits,
        Getters as GettersProduits
    } from '../../../../store/produits'
    import type {FormField, FormValue} from '../../../../types/bootstrap-5'
    import {computed, defineEmits, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const fetchProduits = useNamespacedActions<ActionsProduits>('produits', [
        'fetchProduits'
    ]).fetchProduits
    const fetchCompagnie = useNamespacedActions<ActionsCompagnie>('compagnies', [
        'fetchCompagnie'
    ]).fetchCompagnie

    const optionsProduits = useNamespacedGetters<GettersProduits>('produits', [
        'options'
    ]).options
    const optionsCompagnie = useNamespacedGetters<GettersCompagnie>('compagnies', [
        'options'
    ]).options

    const fields = computed<FormField[]>(() => [
        {
            label: 'Commande client',
            name: 'commande',
            options: optionsProduits.value,
            type: 'select'
        },

        {
            label: 'Date de livraison',
            name: 'date',
            type: 'date'
        },
        {
            label: 'Compagnie gérante',
            name: 'nom ',
            options: optionsCompagnie.value,
            type: 'select'
        },
        {
            label: 'Usine',
            name: 'usine',
            options: optionsCompagnie.value,
            type: 'select'
        },
        {
            label: 'Produit',
            name: 'produit',
            options: optionsProduits.value,
            type: 'select'
        },
        {label: 'Infos publiques', name: 'info'},
        {label: 'Quantite', name: 'qte'}
    ])

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
    onMounted(async () => {
        await fetchProduits()
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
