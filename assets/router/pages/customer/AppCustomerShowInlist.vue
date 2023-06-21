<script setup>
    //inlist
    import generateItems from '../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const fieldsProduits = [
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
    const fieldsCommandes = [
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

    const fieldsOF = [
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
    const fieldsBL = [
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

    const fieldsFactures = [
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
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-products" active icon="product-hunt" brands title="Produits">
                <AppTable :id="route.name" :fields="fieldsProduits" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-commandes" icon="hand-holding-usd" title="Commandes">
                <AppTable :id="route.name" :fields="fieldsCommandes" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-of" icon="file-invoice" title="OF">
                <AppTable :id="route.name" :fields="fieldsOF" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-bl" icon="clipboard-list" title="BL">
                <AppTable :id="route.name" :fields="fieldsBL" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-factures" icon="file-invoice" title="Factures">
                <AppTable :id="route.name" :fields="fieldsFactures" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
