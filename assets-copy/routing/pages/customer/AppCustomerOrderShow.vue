<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/customerOrderItems'
    import type {Actions as BlActions, Getters as BlGetters} from '../../../store/blCustomerOrderItems'
    import type {Actions as FacturesActions, Getters as FacturesGetters} from '../../../store/facturesCustomerOrderItems'
    import type {Actions as OfActions, Getters as OfGetters} from '../../../store/ofCustomerOrderItems'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../types/bootstrap-5'
    import type {TableField} from '../../../types/app-collection-table'

    import {onMounted} from 'vue'

    const BLfields: FormField[] = [
        {label: 'Adresse de livraison ', name: 'AdresseLivraison', type: 'select'},
        {label: 'Date de livraison confirmée', name: 'dateDeLivraisonConfirmée', type: 'date'}
    ]
    const Facturesfields: FormField[] = [
        {label: 'Adresse de facturation', name: 'AdresseFacturation', type: 'select'}
    ]
    const fieldsCommande: TableField[] = [

        {
            create: true,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'réf',
            name: 'ref ',
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
    const OFfields: TableField[] = [

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
    const fieldsBL: TableField[] = [

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
    const fieldsFactures: TableField[] = [

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
    const fetchItem = useNamespacedActions<Actions>('customerOrderItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('customerOrderItems', ['items'])
    console.log('bbbbb', items)
    onMounted(async () => {
        await fetchItem()
    })
    const fetchItemOf = useNamespacedActions<OfActions>('ofCustomerOrderItems', ['fetchItem']).fetchItem
    const {itemsOf} = useNamespacedGetters<OfGetters>('ofCustomerOrderItems', ['itemsOf'])
    console.log('of', items)
    onMounted(async () => {
        await fetchItemOf()
    })
    const fetchItemBl = useNamespacedActions<BlActions>('blCustomerOrderItems', ['fetchItem']).fetchItem
    const {itemsBl} = useNamespacedGetters<BlGetters>('blCustomerOrderItems', ['itemsBl'])
    console.log('Bl', itemsBl)
    onMounted(async () => {
        await fetchItemBl()
    })
    const fetchItemFactures = useNamespacedActions<FacturesActions>('facturesCustomerOrderItems', ['fetchItem']).fetchItem
    const {ItemsFactures} = useNamespacedGetters<FacturesGetters>('facturesCustomerOrderItems', ['ItemsFactures'])
    console.log('Factures', ItemsFactures)
    onMounted(async () => {
        await fetchItemFactures()
    })
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active icon="sitemap" title="Commande">
            <AppCollectionTable :fields="fieldsCommande" :items="items" create/>
        </AppTab>
        <AppTab id="gui-start-files" icon="industry" title="OF">
            <AppCollectionTable :fields="OFfields" :items="itemsOf" create/>
        </AppTab>
        <AppTab id="gui-start-quality" icon="clipboard" title="Bons de préparation">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" icon="truck" title="BL">
            <AppCardShow :fields="BLfields"/>
            <AppCollectionTable :fields="fieldsBL" :items="itemsBl" create/>
        </AppTab>
        <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Factures">
            <AppCardShow :fields="Facturesfields"/>
            <AppCollectionTable :fields="fieldsFactures" :items="ItemsFactures" create/>
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
