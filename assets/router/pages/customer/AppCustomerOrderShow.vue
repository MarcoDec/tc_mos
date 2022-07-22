<script setup>
    import {useBlCustomerOrderItemsStore} from '../../../stores/customer/blCustomerOrderItems'
    import {useCustomerOrderItemsStore} from '../../../stores/customer/customerOrderItems'
    import {useFacturesCustomerOrderItemsStore} from '../../../stores/customer/facturesCustomerOrderItems'
    import {useOfCustomerOrderItemsStore} from '../../../stores/customer/ofCustomerOrderItems'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'

    const BLfields = [
        {label: 'Adresse de livraison ', name: 'AdresseLivraison', type: 'text'},
        {label: 'Date de livraison confirmée', name: 'dateDeLivraisonConfirmée', type: 'date'}
    ]
    const Facturesfields = [
        {label: 'Adresse de facturation', name: 'AdresseFacturation', type: 'text'}
    ]
    const fieldsCommande = [

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
            label: 'réf',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantitéSouhaitée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'date de livraison souhaitée ',
            name: 'dateLivraisonSouhaitée',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité confirmée',
            name: 'quantitéConfirmée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Date de livraison confirmée ',
            name: 'dateLivraisonConfirmée',
            sort: true,
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
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'description',
            name: 'description',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const OFfields = [

        {
            create: true,
            filter: true,
            label: 'Ofnumber',
            name: 'ofnumber',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Manufacturing Date',
            name: 'manufacturingDate',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Manufacturing Company',
            name: 'manufacturingCompany',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantity',
            name: 'quantity',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantity Done',
            name: 'quantityDone',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Delivery Date',
            name: 'deliveryDate',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Current Place',
            name: 'currentPlace',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsBL = [

        {
            create: true,
            filter: true,
            label: 'Number',
            name: 'number',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Departure Date',
            name: 'departureDate',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Current Place',
            name: 'currentPlace',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsFactures = [

        {
            create: true,
            filter: true,
            label: 'Invoice Number',
            name: 'invoiceNumber',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Total HT',
            name: 'totalHT ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Total TTC',
            name: 'totalTTC',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Vta',
            name: 'vta',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Invoice Date',
            name: 'invoiceDate',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Deadline Date ',
            name: 'deadlineDate',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Invoice Send By Email',
            name: 'invoiceSendByEmail',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Current Place',
            name: 'currentPlace',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    storeCustomerOrderItems.fetchItems()

    const storeBlCustomerOrderItems = useBlCustomerOrderItemsStore()
    storeBlCustomerOrderItems.fetchItems()

    const storeFacturesCustomerOrderItems = useFacturesCustomerOrderItemsStore()
    storeFacturesCustomerOrderItems.fetchItems()

    const storeOfCustomerOrderItems = useOfCustomerOrderItemsStore()
    storeOfCustomerOrderItems.fetchItems()
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active icon="sitemap" title="Commande">
            <AppTable :id="route.name" :fields="fieldsCommande" :store="storeCustomerOrderItems" :machine="machine"/>
        </AppTab>
        <AppTab id="gui-start-files" icon="industry" title="OF">
            <AppTable :id="route.name" :fields="OFfields" :store="storeOfCustomerOrderItems" :machine="machine"/>
        </AppTab>
        <AppTab id="gui-start-quality" icon="clipboard" title="Bons de préparation">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" icon="truck" title="BL">
            <AppCardShow id="addBL" :fields="BLfields"/>
            <AppTable :id="route.name" :fields="fieldsBL" :store="storeBlCustomerOrderItems" :machine="machine"/>
        </AppTab>
        <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Factures">
            <AppCardShow id="addFacture" :fields="Facturesfields"/>
            <AppTable :id="route.name" :fields="fieldsFactures" :store="storeFacturesCustomerOrderItems" :machine="machine"/>
        </AppTab>
        <AppTab id="gui-start-addresses" icon="chart-line" title="Qualité">
            <div class="alert alert-warning">
                a définir
            </div>
        </AppTab>
        <AppTab id="gui-start-contacts" icon="clipboard" title="Généralités">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
    </AppTabs>
</template>
