<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {ref} from 'vue'
    const Produits = ref(true)
    const Commandes = ref(false)
    const Of = ref(false)
    const Bl = ref(false)
    const Factures = ref(false)

    function showProduits(): void {
        Produits.value = true
        Commandes.value = false
        Of.value = false
        Bl.value = false
        Factures.value = false
    }
    function showCommandes(): void {
        Produits.value = false
        Commandes.value = true
        Of.value = false
        Bl.value = false
        Factures.value = false
    }
    function showOF(): void {
        Produits.value = false
        Commandes.value = false
        Of.value = true
        Bl.value = false
        Factures.value = false
    }
    function showBL(): void {
        Produits.value = false
        Commandes.value = false
        Of.value = false
        Bl.value = true
        Factures.value = false
    }
    function showFactures(): void {
        Produits.value = false
        Commandes.value = false
        Of.value = false
        Bl.value = false
        Factures.value = true
    }

    const fieldsProduits: TableField[] = [
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
            label: 'Réf.',
            name: 'ref ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Désignation ',
            name: 'désignation ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière commande',
            name: 'dernièreCommande ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernier OF',
            name: 'dernierOf',
            sort: false,
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
    const fieldsCommandes: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Compagnie',
            name: 'compagnie',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf',
            name: 'réf',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison confirmée',
            name: 'DateLivraisonConfirmée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'type de commande',
            name: 'typeCommande',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de validation',
            name: 'dateValidation',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'commande client ',
            name: 'commandeClient ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Compagnie gérante',
            name: 'compagnieGérante',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Usine ',
            name: 'usine',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'N°OF',
            name: 'numeroOF',
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
            label: 'Quantité',
            name: 'quantité',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité fabriquée',
            name: 'quantiteFabriquée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité réelle',
            name: 'quantitéRéelle',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fabrication',
            name: 'dateFabrication',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsBL: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Bordereau ',
            name: 'bordereau ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Title',
            name: 'title',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Départ ',
            name: 'dateDépart',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsFactures: TableField[] = [
        {
            create: false,
            filter: true,
            label: 'Id',
            name: 'id',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Invoice Number',
            name: 'invoiceNumber',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Invoice Date',
            name: 'invoiceDate',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Current Place',
            name: 'currentPlace',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Total HT',
            name: 'totalHT',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Total TTC',
            name: 'totalTTC',
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
                    <button id="v-pills-home-tab" class="active align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-CasEmplois" type="button" role="tab" aria-controls="v-pills-Produits" aria-selected="true" @click="showProduits">
                        <Fa :icon="['fab', 'product-hunt']"/> Produits
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Commandes" type="button" role="tab" aria-controls="v-pills-Commandes" aria-selected="false" @click="showCommandes">
                        <Fa icon="hand-holding-usd"/> Commandes
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-OF" type="button" role="tab" aria-controls="v-pills-OF" aria-selected="false" @click="showOF">
                        <Fa icon="file-invoice"/> OF
                    </button>
                    <button id="v-pills-profile-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-BL" type="button" role="tab" aria-controls="v-pills-BL" aria-selected="false" @click="showBL">
                        <Fa icon="clipboard-list"/> BL
                    </button>
                    <button id="v-pills-messages-tab" class="align-left nav-link" data-bs-toggle="pill" data-bs-target="#v-pills-Factures" type="button" role="tab" aria-controls="v-pills-Factures" aria-selected="false" @click="showFactures ">
                        <Fa icon="file-invoice"/> Factures
                    </button>
                </div>
            </div>
        </div>
        <div v-if="Produits" class="col">
            <AppCollectionTable :fields="fieldsProduits" :items="items"/>
        </div>
        <div v-else-if="Commandes" class="col">
            <AppCollectionTable :fields="fieldsCommandes" :items="items"/>
        </div>
        <div v-else-if="Of" class="col">
            <AppCollectionTable :fields="fieldsOF" :items="items"/>
        </div>
        <div v-else-if="Bl" class="col">
            <AppCollectionTable :fields="fieldsBL" :items="items"/>
        </div>
        <div v-else-if="Factures" class="col">
            <AppCollectionTable :fields="fieldsFactures" :items="items"/>
        </div>
    </div>
</template>
