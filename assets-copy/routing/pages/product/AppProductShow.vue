<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    const Composition = ref(true)
    const Commandes = ref(false)

    function showComposition(): void {
        Composition.value = true
        Commandes.value = false
    }
    function showCommandes(): void {
        Composition.value = false
        Commandes.value = true
    }

    const fieldsComposition: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Ref - Designation',
            name: 'refDesignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Photo.',
            name: 'photo ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Famille',
            name: 'famille ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantity ',
            name: 'quantity ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Unit ',
            name: 'unit ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk site',
            name: 'stkSite',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk total',
            name: 'stkTotal',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'reference',
            name: 'reference',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité confirmée',
            name: 'quantitéConfirmée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantitéSouhaitée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité envoyée',
            name: 'quantitéEnvoyée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'datelivraison',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de livraison souhaitée',
            name: 'datelivraison',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const items: TableItem[] = [
    ]
</script>

<template>
    <div class="row">
        <div class="col-2">
            <div class="align-items-start d-flex">
                <div id="v-pills-tab" class="flex-column me-3 nav nav-pills" role="tablist" aria-orientation="vertical">
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Composition" type="button" role="tab" aria-controls="v-pills-Composition" aria-selected="true" @click="showComposition">
                        <Fa icon="sitemap"/> Composition
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Commandes" type="button" role="tab" aria-controls="v-pills-Commandes" aria-selected="false" @click="showCommandes">
                        <Fa icon="hand-holding-usd"/> Commandes
                    </button>
                </div>
            </div>
        </div>
        <div v-if="Composition" class="col">
            <AppCollectionTable :fields="fieldsComposition" :items="items"/>
        </div>
        <div v-else-if="Commandes" class="col">
            <AppCollectionTable :fields="fieldsCommandes" :items="items"/>
        </div>
    </div>
</template>
