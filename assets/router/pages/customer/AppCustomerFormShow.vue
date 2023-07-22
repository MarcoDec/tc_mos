<script setup>
    import AppShowCustomerTabAccounting from '../../../components/pages/selling/customer/AppShowCustomerTabAccounting.vue'
    import AppShowCustomerTabAddress from '../../../components/pages/selling/customer/AppShowCustomerTabAddress.vue'
    // import AppShowCustomerTabContact from '../../../components/pages/selling/customer/AppShowCustomerTabContact.vue'
    import AppShowCustomerTabGeneral from '../../../components/pages/selling/customer/AppShowCustomerTabGeneral.vue'
    import AppShowCustomerTabLogistic from '../../../components/pages/selling/customer/AppShowCustomerTabLogistic.vue'
    import AppShowCustomerTabQuality from '../../../components/pages/selling/customer/AppShowCustomerTabQuality.vue'
    import AppTabFichiers from '../../../components/tab/AppTabFichiers.vue'
    import {computed} from 'vue'
    import {useCustomerAttachmentStore} from '../../../stores/customers/customerAttachment'
    import {useCustomerContactsStore} from '../../../stores/customers/customerContacts'
    import {useCustomerStore} from '../../../stores/customers/customers'
    import useOptions from '../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../stores/societies/societies'

    const route = useRoute()
    const idCustomer = route.params.id_customer
    const fecthOptions = useOptions('countries')
    await fecthOptions.fetchOp()
    const fetchCustomerStore = useCustomerStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fecthCustomerContactsStore = useCustomerContactsStore()
    await fetchCustomerStore.fetchOne(idCustomer)
    await fetchCustomerStore.fetchInvoiceTime()
    await fetchSocietyStore.fetch()
    const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/))
    const customerId = Number(fetchCustomerStore.customer.id)
    await fetchSocietyStore.fetchById(societyId)
    await fecthCustomerContactsStore.fetchBySociety(societyId)
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchCustomerStore.customer.outstandingMax.code = 'EUR'
    // const dataSuppliers = computed(() =>
    //     Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.society))

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
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabGeneral
                    :data-customers="fetchCustomerStore.customer"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <Suspense>
                <AppTabFichiers
                    attachment-element-label="customer"
                    :element-api-url="`/api/customers/${customerId}`"
                    :element-attachment-store="fetchCustomerAttachmentStore"
                    :element-id="customerId"
                    element-parameter-name="CUSTOMER_ATTACHMENT_CATEGORIES"
                    :element-store="useCustomerStore"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabQuality
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabLogistic
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-accounting"
            title="Comptabilité"
            icon="industry"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabAccounting
                    :data-customers="fetchCustomerStore.customer"
                    :data-society="fetchSocietyStore.society"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresse"
            icon="location-dot"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabAddress
                    :options-countries="optionsCountries"/>
            </Suspense>
        </AppTab>
        <!--        <AppTab-->
        <!--            id="gui-start-contacts"-->
        <!--            title="Contacts"-->
        <!--            icon="file-contract"-->
        <!--            tabs="gui-start">-->
        <!--            <Suspense>-->
        <!--                <AppShowCustomerTabContact-->
        <!--                    :options-countries="optionsCountries"/>-->
        <!--            </Suspense>-->
        <!--        </AppTab>-->
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

