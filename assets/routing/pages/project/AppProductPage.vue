<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/produits'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppProductCreate from './AppProductCreate.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un Produit'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchEngine = useNamespacedActions<Actions>('produits', [
        'fetchProduits'
    ]).fetchProduits
    const {items} = useNamespacedGetters<Getters>('produits', ['items'])

    onMounted(async () => {
        await fetchEngine()
    })

    const fields = [
        {
            create: true,
            filter: true,
            label: 'Img',
            name: 'img',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Famille',
            name: 'famille',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type De Produit',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        },

        {
            create: true,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Stocks',
            name: 'stock',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Besoins enregistrés',
            name: 'besoin',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Date d\'expiration',
            name: 'date',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="product-hunt"/>
            Produits
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppProductCreate/>
    </AppModal>

    <AppCollectionTable
        :id="route.name"
        :fields="fields"
        :items="items"
        pagination>
        <template #etat="{item}">
            <td><AppTrafficLight :item="item"/></td>
        </template>
    </AppCollectionTable>
</template>
