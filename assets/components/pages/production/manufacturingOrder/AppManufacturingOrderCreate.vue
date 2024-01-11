<script setup>
    import {computed, onMounted} from 'vue-demi'
    import useCompagnies from '../../../../stores/compagnie/compagnies'
    import useProducts from '../../../../stores/product/products'

    const emit = defineEmits(['update:modelValue'])
    const products = useProducts()
    const compagnies = useCompagnies()
    onMounted(async () => {
        await products.fetch()
        await compagnies.fetch()
    })
    const fields = computed(() => [
        {
            label: 'Commande client',
            name: 'commande',
            options: products,
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
            options: compagnies,
            type: 'select'
        },
        {
            label: 'Usine',
            name: 'usine',
            options: compagnies,
            type: 'select'
        },
        {
            label: 'Produit',
            name: 'produit',
            options: products,
            type: 'select'
        },
        {label: 'Infos publiques', name: 'info'},
        {label: 'Quantite', name: 'qte'}
    ])

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <AppForm id="order" :fields="fields" @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
