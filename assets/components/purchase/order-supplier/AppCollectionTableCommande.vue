<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/supplierItems'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import {onMounted} from 'vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const fields
        = [
            {
                label: 'N'
            },
            {
                create: false,
                filter: true,
                label: 'Composant',
                name: 'composant',
                options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                sort: false,
                type: 'select',
                update: true
            },
            {
                create: false,
                filter: true,
                label: 'Produit',
                name: 'produit',
                options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                sort: false,
                type: 'select',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Référence Fournisseur',
                name: 'ref',
                sort: true,
                type: 'text',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Quantité Souhaitée',
                name: 'quantiteS',
                sort: true,
                type: 'text',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Date Souhaitée',
                name: 'date',
                sort: false,
                type: 'date',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Quantité',
                name: 'quantite',
                sort: true,
                type: 'text',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Date de confirmation',
                name: 'date',
                sort: false,
                type: 'date',
                update: true
            },
            {
                create: true,
                filter: true,
                label: 'Etat',
                name: 'etat',
                sort: true,
                type: 'text',
                update: false
            },
            {
                create: true,
                filter: true,
                label: 'Texte',
                name: 'texte',
                sort: true,
                type: 'text',
                update: false
            },
            {
                create: false,
                filter: true,
                label: 'Compagnie destinataire',
                name: 'compagnie',
                options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                sort: false,
                type: 'select',
                update: true
            }

        ]

    const fetchItem = useNamespacedActions<Actions>('supplierItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('supplierItems', ['items'])

    onMounted(async () => {
        await fetchItem()
    })
</script>

<template>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

