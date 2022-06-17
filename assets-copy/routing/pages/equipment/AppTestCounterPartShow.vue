<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    const TypesMaintenances = ref(true)
    const HistoriqueMaintenances = ref(false)
    const Of = ref(false)
    const PiecesRechange = ref(false)

    function showTypesMaintenances(): void {
        TypesMaintenances.value = true
        HistoriqueMaintenances.value = false
        Of.value = false
        PiecesRechange.value = false
    }
    function showHistoriqueMaintenances(): void {
        TypesMaintenances.value = false
        HistoriqueMaintenances.value = true
        Of.value = false
        PiecesRechange.value = false
    }
    function showOF(): void {
        TypesMaintenances.value = false
        HistoriqueMaintenances.value = false
        Of.value = true
        PiecesRechange.value = false
    }
    function showPiecesRechange(): void {
        TypesMaintenances.value = false
        HistoriqueMaintenances.value = false
        Of.value = false
        PiecesRechange.value = true
    }

    const fieldsTypesMaintenances: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Opération ',
            name: 'opération ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Fréquence',
            name: 'fréquence',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Période ',
            name: 'période ',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsHistoriqueMaintenances: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Maintenance de l équipement',
            name: 'maintenanceEquipement',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date d éxecution',
            name: 'dateExecution',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF: TableField[] = [
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
            label: 'Ref.Prod',
            name: 'refProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ind.Prod',
            name: 'indProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'OF',
            name: 'OF',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Responsable',
            name: 'opeResponsable',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Employés',
            name: 'opeEmployés',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Date de début',
            name: 'opeDateDébut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Date de fin',
            name: 'opeDateFin',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsPiecesRechange: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Désignation ',
            name: 'désignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Sous Famille de Composant',
            name: 'sousFamilleComposant',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Status',
            name: 'status',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stocks',
            name: 'stocks',
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
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-TypesMaintenances" type="button" role="tab" aria-controls="v-pills-TypesMaintenances" aria-selected="true" @click="showTypesMaintenances">
                        <Fa icon="paint-roller"/> Types de maintenances
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-HistoriqueMaintenances" type="button" role="tab" aria-controls="v-pills-HistoriqueMaintenances" aria-selected="false" @click="showHistoriqueMaintenances">
                        <Fa icon="history"/> Historique des maintenances
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-PiecesRechange" type="button" role="tab" aria-controls="v-pills-PiecesRechange" aria-selected="false" @click="showPiecesRechange">
                        <Fa icon="retweet"/> Pièces de rechange
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-OF" type="button" role="tab" aria-controls="v-pills-OF" aria-selected="false" @click="showOF">
                        <Fa icon="file-invoice"/> OF
                    </button>
                </div>
            </div>
        </div>
        <div v-if="TypesMaintenances" class="col">
            <AppCollectionTable :fields="fieldsTypesMaintenances" :items="items"/>
        </div>
        <div v-else-if="HistoriqueMaintenances" class="col">
            <AppCollectionTable :fields="fieldsHistoriqueMaintenances" :items="items"/>
        </div>
        <div v-else-if="Of" class="col">
            <AppCollectionTable :fields="fieldsOF" :items="items"/>
        </div>
        <div v-else-if="PiecesRechange" class="col">
            <AppCollectionTable :fields="fieldsPiecesRechange" :items="items"/>
        </div>
    </div>
</template>
