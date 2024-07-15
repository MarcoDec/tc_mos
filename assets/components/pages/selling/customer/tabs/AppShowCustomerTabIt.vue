<script setup>
    // import generateCustomer from '../../../../../stores/selling/customers/customer'
    // import generateSocieties from '../../../../../stores/management/societies/societie'
    import {computed, ref, watch} from 'vue'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'

    const props = defineProps({
        dataCustomers: {required: true, type: Object}
    })
    const user = useUser()
    const isItAdmin = user.isItAdmin
    // console.log('Informations Client', props.dataCustomers)
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
    //region select Famille EDI
    const EDI_WEB = 'webEDI'
    const EDI_INTEGRATED = 'integratedEDI'
    const ediKindOptions = {
        //label: value => null,
        options: [
            {text: 'Non défini', value: null},
            {text: 'EDI web', value: EDI_WEB},
            {text: 'EDI intégré', value: EDI_INTEGRATED}
        ]
    }
    //endregion
    //region select Standard EDI
    //ORDERS/DELFOR
    const ediTypesOptions = {
        options: [
            {text: 'Non défini', value: null},
            {text: 'ORDERS', value: 'ORDERS'},
            {text: 'DELFOR', value: 'DELFOR'}
        ]
    }
    //endregion
    //region select progression EDI
    //DEFINITION, VALIDATION, ACTIVE, DISABLED
    const ediMaturityOptions = {
        options: [
            {text: 'Non défini', value: null},
            {text: 'DEFINITION', value: 'DEFINITION'},
            {text: 'VALIDATION', value: 'VALIDATION'},
            {text: 'ACTIVE', value: 'ACTIVE'},
            {text: 'DISABLED', value: 'DISABLED'}
        ]
    }
    //endregion
    localData.value = {
        isEdiOrders: props.dataCustomers.isEdiOrders,
        ediKind: props.dataCustomers.ediKind
    }
    // console.log('localData', props.dataCustomers.ediKind)
    const localWebEdiData = ref({})
    localWebEdiData.value = {
        webEdiUrl: props.dataCustomers.webEdiUrl,
        webEdiInfos: props.dataCustomers.webEdiInfos,
        ediOrderType: props.dataCustomers.ediOrderType
    }
    const integratedEdiData = ref({})
    integratedEdiData.value = {
        ediOrderType: props.dataCustomers.ediOrderType,
        ediOrdersMaturity: props.dataCustomers.ediOrdersMaturity,
        isEdiAsn: props.dataCustomers.isEdiAsn
    }
    const ediFields = computed(
        () => {
            if (localData.value.isEdiOrders)
                return [
                    {label: 'Gestion EDI', name: 'isEdiOrders', type: 'boolean'},
                    {label: 'Famille d\'EDI', name: 'ediKind', type: 'select', options: ediKindOptions}
                ]
            return [{label: 'Gestion EDI', name: 'isEdiOrders', type: 'boolean'}]
        }
    )
    const webEdiFields = [
        {label: 'Url WEB EDI', name: 'webEdiUrl', type: 'text'},
        {label: 'Infos WEB EDI', name: 'webEdiInfos', type: 'textarea'},
        {label: 'Standard EDI', name: 'ediOrderType', type: 'text'}
    ]
    const integratedEdiFields = [
        {label: 'Standard EDI', name: 'ediOrderType', type: 'select', options: ediTypesOptions},
        {label: 'Gestion ASN', name: 'isEdiAsn', type: 'boolean'},
        {label: 'Progression Définition EDI', name: 'ediOrdersMaturity', type: 'select', options: ediMaturityOptions}
    ]
    async function updateEdiFields() {
        console.log('ediKind', localData.value.ediKind)
        const data = {
            isEdiOrders: localData.value.isEdiOrders,
            ediKind: localData.value.ediKind === null ? null : localData.value.ediKind
        }
        await fetchCustomerStore.update(data, 'it')
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateWebEdiFields() {
        const data = {
            webEdiUrl: localWebEdiData.value.webEdiUrl,
            webEdiInfos: localWebEdiData.value.webEdiInfos,
            ediOrderType: localWebEdiData.value.ediOrderType
        }
        // const itemSoc = generateSocieties(props.dataSociety)
        // await itemSoc.update(data)
        await fetchCustomerStore.update(data, 'selling')
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateIntegratedEdiFields() {
        const data = {
            isEdiAsn: integratedEdiData.value.isEdiAsn,
            ediOrderType: integratedEdiData.value.ediOrderType,
            ediOrdersMaturity: integratedEdiData.value.ediOrdersMaturity
        }
        await fetchCustomerStore.update(data, 'it')
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
    function updateLocalWebEdiData(value) {
        localWebEdiData.value = value
    }
    function updateLocalIntegratedEdiData(value) {
        integratedEdiData.value = value
    }
    watch(() => localData.value, newValue => {
        if (!newValue.isEdiOrders) {
            localData.value.ediKind = null
        }
    })
    function onCancelEdi(){
        localData.value = {
            isEdiOrders: props.dataCustomers.isEdiOrders,
            ediKind: props.dataCustomers.ediKind
        }
    }
    function onCancelWebEdi() {
        localWebEdiData.value = {
            webEdiUrl: props.dataCustomers.webEdiUrl,
            webEdiInfos: props.dataCustomers.webEdiInfos,
            ediOrderType: props.dataCustomers.ediOrderType
        }
    }
    function onCancelIntegratedEdi() {
        integratedEdiData.value = {
            ediOrderType: props.dataCustomers.ediOrderType,
            ediOrdersMaturity: props.dataCustomers.ediOrdersMaturity,
            isEdiAsn: props.dataCustomers.isEdiAsn
        }
    }
</script>

<template>
    <AppSuspense>
        <div class="tabEdi">
            <AppCardShow
                id="addEdi"
                class="ediCard"
                :enabled="isItAdmin"
                :fields="ediFields"
                :component-attribute="localData"
                title="Paramètres Edi"
                @cancel="onCancelEdi"
                @update="updateEdiFields"
                @update:model-value="updateLocalData"/>
            <AppCardShow
                v-if="localData.isEdiOrders && localData.ediKind === EDI_WEB"
                id="addWebEdi"
                class="ediCard"
                :enabled="isItAdmin"
                :fields="webEdiFields"
                :component-attribute="localWebEdiData"
                title="Paramètres Web Edi"
                @cancel="onCancelWebEdi"
                @update="updateWebEdiFields"
                @update:model-value="updateLocalWebEdiData"/>
            <AppCardShow
                v-if="localData.isEdiOrders && localData.ediKind === EDI_INTEGRATED"
                id="addIntegratedEdi"
                class="ediCard"
                :enabled="isItAdmin"
                :fields="integratedEdiFields"
                :component-attribute="integratedEdiData"
                title="Paramètres Edi intégré"
                @cancel="onCancelIntegratedEdi"
                @update="updateIntegratedEdiFields"
                @update:model-value="updateLocalIntegratedEdiData"/>
        </div>
    </AppSuspense>
</template>

<style scoped>
div.tabEdi {
    display: flex;
    justify-content: center;
    flex-wrap: wrap
}
.ediCard {
    min-width: 500px;
    margin-left: 20px;
    margin-bottom: 20px;
}
</style>
