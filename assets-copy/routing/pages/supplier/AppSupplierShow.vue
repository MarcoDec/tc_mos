<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    // import {defineProps} from 'vue'
    // import {useRoute} from 'vue-router'
    const Fournitures = ref(true)
    const Réceptions = ref(false)
    const Commandes = ref(false)

    function showFournitures(): void {
        Fournitures.value = true
        Réceptions.value = false
        Commandes.value = false
    }
    function showRéceptions(): void {
        Fournitures.value = false
        Réceptions.value = true
        Commandes.value = false
    }
    function showCommandes(): void {
        Fournitures.value = false
        Réceptions.value = false
        Commandes.value = true
    }

    const fieldsFournitures: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref. Fournisseur ',
            name: 'refFournisseur ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantité ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Texte ',
            name: 'texte ',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsRéceptions: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Crée le ',
            name: 'créeLe ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Retard ',
            name: 'retard',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Evènement ',
            name: 'evènement',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Message',
            name: 'message',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde Fournisseur Ferme',
            name: 'fournisseurFerme',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde de Composant Fournisseur',
            name: 'composantFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'référence',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
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
        },
        {
            create: false,
            filter: true,
            label: 'Date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière Réception',
            name: 'dernièreRéception',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut de Cmde Fournisseur',
            name: 'statutFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément Fret',
            name: 'supplémentFret',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Commentaire',
            name: 'Commentaire',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Info Public',
            name: 'infoPublic',
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
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Fournitures" type="button" role="tab" aria-controls="v-pills-Fournitures" aria-selected="true" @click="showFournitures">
                        <Fa icon="puzzle-piece"/> Fournitures
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Réceptions" type="button" role="tab" aria-controls="v-pills-Réceptions" aria-selected="false" @click="showRéceptions">
                        <Fa icon="receipt"/> Réceptions
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Commandes" type="button" role="tab" aria-controls="v-pills-Commandes" aria-selected="false" @click="showCommandes">
                        <Fa icon="shopping-cart"/> Commandes
                    </button>
                </div>
            </div>
        </div>
        <div v-if="Fournitures" class="col">
            <AppCollectionTable :fields="fieldsFournitures" :items="items"/>
        </div>
        <div v-else-if="Réceptions" class="col">
            <AppCollectionTable :fields="fieldsRéceptions" :items="items"/>
        </div>
        <div v-else-if="Commandes" class="col">
            <AppCollectionTable :fields="fieldsCommandes" :items="items"/>
        </div>
    </div>
</template>

