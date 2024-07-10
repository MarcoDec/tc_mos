<script setup>
    import AppShowCustomerTabAccounting from './tabs/AppShowCustomerTabAccounting.vue'
    import AppShowCustomerTabAddress from './tabs/AppShowCustomerTabAddress.vue'
    import AppShowCustomerTabContact from './tabs/AppShowCustomerTabContact.vue'
    import AppShowCustomerTabLogistic from './tabs/AppShowCustomerTabLogistic.vue'
    import AppShowCustomerTabQuality from './tabs/AppShowCustomerTabQuality.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'
    import {computed} from 'vue'
    import {useCustomerAttachmentStore} from '../../../../stores/selling/customers/customerAttachment'
    import {useCustomerContactsStore} from '../../../../stores/selling/customers/customerContacts'
    import {useCustomerStore} from '../../../../stores/selling/customers/customers'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../../stores/management/societies/societies'
    import {useInvoiceTimeDuesStore} from '../../../../stores/management/invoiceTimeDues'
    import AppTab from '../../../tab/AppTab.vue'
    import AppShowCustomerTabIt from './tabs/AppShowCustomerTabIt.vue'
    import AppPricesTablePage from "../../prices/AppPricesTablePage.vue";
    import useUser from "../../../../stores/security";

    const route = useRoute()
    const idCustomer = route.params.id_customer
    const fecthOptions = useOptions('countries')
    await fecthOptions.fetchOp()
    const fetchCustomerStore = useCustomerStore()
    const fetchInvoiceTime = useInvoiceTimeDuesStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fecthCustomerContactsStore = useCustomerContactsStore()
    await fetchCustomerStore.fetchOne(idCustomer)
    await fetchInvoiceTime.fetchInvoiceTime()
    const invoiceData = fetchInvoiceTime.invoicesData
    fetchCustomerStore.invoicesData = invoiceData
    const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/))
    const customerId = Number(fetchCustomerStore.customer.id)
    await fetchSocietyStore.fetchById(societyId)
    await fecthCustomerContactsStore.fetchBySociety(societyId)
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchCustomerStore.customer.outstandingMax.code = 'EUR'

    const user = useUser()
    const isSellingAdmin = user.isSellingAdmin
    const isSellingWriter = user.isSellingWriter
    const rights = {
        main: {
            add: isSellingWriter,
            update: isSellingWriter,
            delete: isSellingAdmin
        },
        price: {
            add: isSellingWriter,
            update: isSellingWriter,
            delete: isSellingAdmin
        }
    }

    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            return {text, value}
        }))
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-files"
            active
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppSuspense>
                <AppTabFichiers
                    attachment-element-label="customer"
                    :element-api-url="`/api/customers/${customerId}`"
                    :element-attachment-store="fetchCustomerAttachmentStore"
                    :element-id="customerId"
                    element-parameter-name="CUSTOMER_ATTACHMENT_CATEGORIES"
                    :element-store="useCustomerStore"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppSuspense>
                <AppShowCustomerTabQuality
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppSuspense>
                <AppShowCustomerTabLogistic
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-prices" title="Prix" tabs="gui-start" icon="euro-sign">
            <AppPricesTablePage
                :customer="`/api/customers/${customerId}`"
                :rights="rights"/>
        </AppTab>
        <AppTab
            id="gui-start-accounting"
            title="Comptabilité"
            icon="industry"
            tabs="gui-start">
            <AppSuspense>
                <AppShowCustomerTabAccounting
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresses"
            icon="location-dot"
            tabs="gui-start">
            <AppSuspense>
                <AppShowCustomerTabAddress
                    :customer-id="customerId"
                    :options-countries="optionsCountries"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppSuspense>
                <AppShowCustomerTabContact
                    :options-countries="optionsCountries"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-IT" title="IT" tabs="gui-start" icon="laptop">
            <AppSuspense>
                <AppShowCustomerTabIt
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
    #gui-start-production, #gui-start-droits {
        padding-bottom: 150px;
    }
</style>

