<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/manufacturingOrders'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppManufacturingOrderCreate from './AppManufacturingOrderCreate.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un OF'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchManufacturingOrder = useNamespacedActions<Actions>('manufacturingOrders', [
        'fetchManufacturingOrder'
    ]).fetchManufacturingOrder
    const {items} = useNamespacedGetters<Getters>('manufacturingOrders', ['items'])

    onMounted(async () => {
        await fetchManufacturingOrder()
    })

    const fields = [
        {
            create: true,
            filter: true,
            label: 'N OF',
            name: 'numero',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Indice',
            name: 'indice',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantite',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantite fabriquée',
            name: 'quantiteF',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Usine',
            name: 'usine',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Commande client',
            name: 'commande',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Compagnie',
            name: 'compagnie',
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
            <Fa class="me-3" icon="tools"/>
            OFs
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppManufacturingOrderCreate/>
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
