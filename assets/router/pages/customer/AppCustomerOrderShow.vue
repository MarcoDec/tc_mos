<script setup>
    import {computed, ref} from 'vue-demi'
    import {useBlCustomerOrderItemsStore} from '../../../stores/customer/blCustomerOrderItems'
    import {useCustomerOrderItemsStore} from '../../../stores/customer/customerOrderItems'
    import {useFacturesCustomerOrderItemsStore} from '../../../stores/customer/facturesCustomerOrderItems'
    import {useOfCustomerOrderItemsStore} from '../../../stores/customer/ofCustomerOrderItems'
    import {useRoute} from 'vue-router'
    import AppTab from '../../../components/tab/AppTab.vue'
    import AppTabs from '../../../components/tab/AppTabs.vue'
    import useFetchCriteria from '../../../stores/fetch-criteria/fetchCriteria'
    import useUser from '../../../stores/security'
    import AppSuspense from '../../../components/AppSuspense.vue'


    const BLfields = [
        {label: 'Adresse de livraison ', name: 'AdresseLivraison', type: 'text'},
        {label: 'Date de livraison confirmée', name: 'dateDeLivraisonConfirmée', type: 'date'}
    ]
    const Facturesfields = [
        {label: 'Adresse de facturation', name: 'AdresseFacturation', type: 'text'}
    ]
    const fieldsCommande = [
        {label: 'Produit', name: 'product', trie: true, type: 'text'},
        {label: 'Réf', name: 'ref', trie: true, type: 'text'},
        {label: 'Quantité souhaitée', name: 'requestedQuantity', trie: true, type: 'text'},
        {label: 'date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date'},
        {label: 'Quantité confirmée', name: 'confirmedQuantity', trie: true, type: 'text'},
        {label: 'Date de livraison confirmée', name: 'confirmedDate', trie: true, type: 'date'},
        {label: 'Etat', name: 'state', trie: true, type: 'text'},
        {label: 'Description', name: 'notes', trie: true, type: 'text'}
    ]
    const OFfields = [
        {label: 'Ofnumber', name: 'ofnumber', trie: true, type: 'text'},
        {label: 'Manufacturing Date', name: 'manufacturingDate', trie: true, type: 'date'},
        {label: 'Manufacturing Company', name: 'manufacturingCompany', trie: true, type: 'text'},
        {label: 'Quantity', name: 'quantity', trie: true, type: 'number'},
        {label: 'Quantity Done', name: 'quantityDone', trie: true, type: 'number'},
        {label: 'Delivery Date', name: 'deliveryDate', trie: true, type: 'date'},
        {label: 'Current Place', name: 'currentPlace', trie: true, type: 'text'}
    ]
    const fieldsBL = [
        {label: 'Number', name: 'number', trie: true, type: 'number'},
        {label: 'Departure Date', name: 'departureDate', trie: true, type: 'date'},
        {label: 'Current Place', name: 'currentPlace', trie: true, type: 'text'}
    ]
    const fieldsFactures = [
        {label: 'Invoice Number', name: 'invoiceNumber', trie: true, type: 'text'},
        {label: 'Total HT', name: 'totalHT', trie: true, type: 'text'},
        {label: 'Total TTC', name: 'totalTTC', trie: true, type: 'text'},
        {label: 'Vta', name: 'vta', trie: true, type: 'text'},
        {label: 'Invoice Date', name: 'invoiceDate', trie: true, type: 'date'},
        {label: 'Deadline Date', name: 'deadlineDate', trie: true, type: 'date'},
        {label: 'Invoice Send By Email', name: 'invoiceSendByEmail', trie: true, type: 'text'},
        {label: 'Current Place', name: 'currentPlace', trie: true, type: 'text'}
    ]
    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const roleuser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')

    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    storeCustomerOrderItems.fetch()

    const customerOrderCriteria = useFetchCriteria('customer-orders-criteria')
    customerOrderCriteria.addFilter('company', currentCompany)
    async function refreshTable() {
        await storeCustomerOrderItems.fetch(customerOrderCriteria.getFetchCriteria)
    }
    await refreshTable()

    const customerOrderItems = computed(()=> storeCustomerOrderItems.itemsCustomerOrders)
    console.log('customerOrderItems', customerOrderItems);
    async function deleted(id){
        await storeCustomerOrderItems.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        console.log('nPage', nPage);
        customerOrderCriteria.gotoPage(parseFloat(nPage))
        await storeCustomerOrderItems.fetch(customerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        customerOrderCriteria.resetAllFilter()
        customerOrderCriteria.addFilter('company', currentCompany)
        await storeCustomerOrderItems.fetch(customerOrderCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        customerOrderCriteria.addSort(payload.name, payload.direction)
        await storeCustomerOrderItems.fetch(customerOrderCriteria.getFetchCriteria)
    }



    // const storeBlCustomerOrderItems = useBlCustomerOrderItemsStore()
    // storeBlCustomerOrderItems.fetchItems()

    // const storeFacturesCustomerOrderItems = useFacturesCustomerOrderItemsStore()
    // storeFacturesCustomerOrderItems.fetchItems()

    // const storeOfCustomerOrderItems = useOfCustomerOrderItemsStore()
    // storeOfCustomerOrderItems.fetchItems()
</script>

<template>
    <AppTabs id="gui-form-create" class="display-block-important">
        <AppTab id="gui-start-main" active icon="sitemap" title="Commande" tabs="gui-form-create">
            <AppSuspense>
                <AppCardableTable
                        :current-page="storeCustomerOrderItems.currentPage"
                        :fields="fieldsCommande"
                        :first-page="storeCustomerOrderItems.firstPage"
                        :items="customerOrderItems"
                        :last-page="storeCustomerOrderItems.lastPage"
                        :next-page="storeCustomerOrderItems.nextPage"
                        :pag="storeCustomerOrderItems.pagination"
                        :previous-page="storeCustomerOrderItems.previousPage"
                        :user="roleuser"
                        form="formCustomerOrdersTable"
                        @deleted="deleted"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @cancel-search="cancelSearch"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-files" icon="industry" title="OF" tabs="gui-form-create">
            <!-- <AppCardableTable :fields="OFfields" :store="storeOfCustomerOrderItems"/> -->
            <!-- <AppTable :id="route.name" :fields="OFfields" :store="storeOfCustomerOrderItems" /> -->
        </AppTab>
        <AppTab id="gui-start-quality" icon="clipboard" title="Bons de préparation" tabs="gui-form-create">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" icon="truck" title="BL" tabs="gui-form-create">
            <AppCardShow id="addBL" :fields="BLfields"/>
            <!-- <AppCardableTable :fields="fieldsBL" :store="storeBlCustomerOrderItems"/> -->
            <!-- <AppTable :id="route.name" :fields="fieldsBL" :store="storeBlCustomerOrderItems" /> -->
        </AppTab>
        <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Factures" tabs="gui-form-create">
            <AppCardShow id="addFacture" :fields="Facturesfields"/>
            <!-- <AppCardableTable :fields="fieldsFactures" :store="storeFacturesCustomerOrderItems"/> -->
            <!-- <AppTable :id="route.name" :fields="fieldsFactures" :store="storeFacturesCustomerOrderItems" /> -->
        </AppTab>
        <AppTab id="gui-start-addresses" icon="chart-line" title="Qualité" tabs="gui-form-create">
            <div class="alert alert-warning">
                a définir
            </div>
        </AppTab>
        <AppTab id="gui-start-contacts" icon="clipboard" title="Généralités" tabs="gui-form-create">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
    </AppTabs>
</template>
<style scoped>
.display-block-important {
    display: block !important;
}
</style>