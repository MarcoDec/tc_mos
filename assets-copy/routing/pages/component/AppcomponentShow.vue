<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    const CasEmplois = ref(true)
    const OutilsConsommateurs = ref(false)
    const Commandes = ref(false)
    const Attributs = ref(false)

    function showCasEmplois(): void {
        CasEmplois.value = true
        OutilsConsommateurs.value = false
        Commandes.value = false
        Attributs.value = false
    }
    function showOutilsConsommateurs(): void {
        CasEmplois.value = false
        OutilsConsommateurs.value = true
        Commandes.value = false
        Attributs.value = false
    }
    function showCommandes(): void {
        CasEmplois.value = false
        OutilsConsommateurs.value = false
        Commandes.value = true
        Attributs.value = false
    }
    function showAttributs(): void {
        CasEmplois.value = false
        OutilsConsommateurs.value = false
        Commandes.value = false
        Attributs.value = true
    }

    const fieldsCasEmplois: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Mandat ',
            name: 'mandat ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantité ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Unité ',
            name: 'unité ',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const fieldsOutilsConsommateurs: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Nom ',
            name: 'name ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref',
            name: 'ref',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Description ',
            name: 'description',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Compagnie ',
            name: 'compagnie',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'engineGroup.label',
            name: 'engineGroup',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Commande',
            name: 'commande',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf. fourn.',
            name: 'RéfFourn',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité demandée',
            name: 'quantitéDemandée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité reçue',
            name: 'quantitéReçue',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité restante',
            name: 'quantitéRestante',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Demandée',
            name: 'dateDemandée',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsAttributs: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Colors',
            name: 'colors',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Composition',
            name: 'composition',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Diamètre Extérieur',
            name: 'diamètreExtérieur',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Matériel',
            name: 'matériel',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nombre',
            name: 'nombre',
            sort: false,
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Section',
            name: 'section',
            sort: true,
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
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-CasEmplois" type="button" role="tab" aria-controls="v-pills-CasEmplois" aria-selected="true" @click="showCasEmplois">
                        <Fa :icon="['fab', 'product-hunt']"/> Cas d'emplois
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-OutilsConsommateurs" type="button" role="tab" aria-controls="v-pills-OutilsConsommateurs" aria-selected="false" @click="showOutilsConsommateurs">
                        <Fa icon="tools"/> Outils consommateurs
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Commandes" type="button" role="tab" aria-controls="v-pills-Commandes" aria-selected="false" @click="showCommandes">
                        <Fa icon="shopping-cart"/> Commandes
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Attributs" type="button" role="tab" aria-controls="v-pills-Attributs" aria-selected="false" @click="showAttributs ">
                        Attributs
                    </button>
                </div>
            </div>
        </div>
        <div v-if="CasEmplois" class="col">
            <AppCollectionTable :fields="fieldsCasEmplois" :items="items"/>
        </div>
        <div v-else-if="OutilsConsommateurs" class="col">
            <AppCollectionTable :fields="fieldsOutilsConsommateurs" :items="items"/>
        </div>
        <div v-else-if="Commandes" class="col">
            <AppCollectionTable :fields="fieldsCommandes" :items="items"/>
        </div>
        <div v-else-if="Attributs" class="col">
            <AppCollectionTable :fields="fieldsAttributs" :items="items"/>
        </div>
    </div>
</template>
