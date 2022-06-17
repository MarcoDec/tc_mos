<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    const Formations = ref(true)
    const Evenements = ref(false)
    const Of = ref(false)
    const Equipements = ref(false)
    const Factures = ref(false)

    function showFormations(): void {
        Formations.value = true
        Evenements.value = false
        Of.value = false
        Equipements.value = false
        Factures.value = false
    }
    function showEvenements(): void {
        Formations.value = false
        Evenements.value = true
        Of.value = false
        Equipements.value = false
        Factures.value = false
    }
    function showOF(): void {
        Formations.value = false
        Evenements.value = false
        Of.value = true
        Equipements.value = false
        Factures.value = false
    }
    function showEquipements(): void {
        Formations.value = false
        Evenements.value = false
        Of.value = false
        Equipements.value = true
        Factures.value = false
    }
    function showFactures(): void {
        Formations.value = false
        Evenements.value = false
        Of.value = false
        Equipements.value = false
        Factures.value = true
    }

    const fieldsFormations: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Sujet ',
            name: 'sujet ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de début',
            name: 'dateDébut ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fin ',
            name: 'dateFin ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de rappel',
            name: 'dateRappel  ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Niveau',
            name: 'niveau ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'formateur',
            name: 'Formateur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière facture',
            name: 'dernièreFacture ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix',
            name: 'prix ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsEvenements: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Date de l événement',
            name: 'dateEvénement',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Description ',
            name: 'description ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Motif',
            name: 'motif',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'OF',
            name: 'OF',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité fabriquée ',
            name: 'quantitéFabriquée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Rebus',
            name: 'rebus',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cadence',
            name: 'cadence',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsFactures: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'date de début ',
            name: 'dateDébut',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de fin',
            name: 'dateFin',
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
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Formations" type="button" role="tab" aria-controls="v-pills-Formations" aria-selected="true" @click="showFormations">
                        Formations
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Evenements" type="button" role="tab" aria-controls="v-pills-Evenements" aria-selected="false" @click="showEvenements">
                        Événements
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-OF" type="button" role="tab" aria-controls="v-pills-OF" aria-selected="false" @click="showOF">
                        OF
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Equipements" type="button" role="tab" aria-controls="v-pills-Equipements" aria-selected="false" @click="showEquipements">
                        <Fa icon="tools"/> Équipements
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Pointage" type="button" role="tab" aria-controls="v-pills-Pointage" aria-selected="false" @click="showFactures ">
                        Pointage
                    </button>
                </div>
            </div>
        </div>
        <div v-if="Formations" class="col">
            <AppCollectionTable :fields="fieldsFormations" :items="items"/>
        </div>
        <div v-else-if="Evenements" class="col">
            <AppCollectionTable :fields="fieldsEvenements" :items="items"/>
        </div>
        <div v-else-if="Of" class="col">
            <AppCollectionTable :fields="fieldsOF" :items="items"/>
        </div>
        <div v-else-if="Equipements" class="col"/>
        <div v-else-if="Factures" class="col">
            <AppCollectionTable :fields="fieldsFactures" :items="items"/>
        </div>
    </div>
</template>
