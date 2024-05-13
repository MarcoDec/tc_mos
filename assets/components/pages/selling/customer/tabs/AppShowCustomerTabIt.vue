<script setup>
    // import generateCustomer from '../../../../../stores/selling/customers/customer'
    import generateSocieties from '../../../../../stores/management/societies/societie'
    import {computed, ref, watch} from 'vue'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import useUser from '../../../../../stores/security'
    import AppSuspense from "../../../../AppSuspense.vue";

    const props = defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    })
    const user = useUser()
    const isItAdmin = user.isItAdmin
    console.log('Informations Client', props.dataCustomers)
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
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
    localData.value = {
        isEdiOrders: props.dataCustomers.isEdiOrders,
        ediKind: props.dataCustomers.ediKind
    }
    console.log('localData', props.dataCustomers.ediKind)
    const localWebEdiData = ref({})
    localWebEdiData.value = {
        webEdiUrl: props.dataCustomers.webEdiUrl,
        webEdiInfo: props.dataCustomers.webEdiInfo,
        ediOrdersType: props.dataCustomers.ediOrdersType,
    }
    const integratedEdiData = ref ({})
    integratedEdiData.value = {
        ediOrdersType: props.dataCustomers.ediOrdersType,
        ediOrdersMaturity: props.dataCustomers.ediOrdersMaturity,
        isEdiAsn: props.dataCustomers.isEdiAsn
    }
    const ediFields = computed(
        () => localData.value.isEdiOrders ?
            [
                {label: 'Gestion EDI', name: 'isEdiOrders', type: 'boolean'},
                {label: 'Famille d\'EDI', name: 'ediKind', type: 'select', options: ediKindOptions}
            ] : [ {label: 'Gestion EDI', name: 'isEdiOrders', type: 'boolean'}]
    )
    const webEdiFields = [
        {label: 'Url WEB EDI', name: 'webEdiUrl', type: 'text'},
        {label: 'Infos WEB EDI', name: 'webEdiInfo', type: 'textarea'},
        {label: 'Standard EDI', name: 'ediOrdersType', type: 'text'}
    ]
    const integratedEdiFields = [
        {label: 'Standard EDI', name: 'ediOrdersType', type: 'text'},
        {label: 'Progression Définition EDI', name: 'ediOrdersMaturity', type: 'text'},
        {label: 'Gestion ASN', name: 'isEdiAsn', type: 'boolean'}
    ]
    async function updateEdiFields() {
        console.log('ediKind', localData.value.ediKind)
        const data = {
            isEdiOrders: localData.value.isEdiOrders,
            ediKind: localData.value.ediKind === null ? null : localData.value.ediKind
        }
        // const itemSoc = generateSocieties(props.dataSociety)
        // await itemSoc.update(data)
        await fetchCustomerStore.update(data, 'it')
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateWebEdiFields() {
        const data = {
            webEdiUrl: localData.value.webEdiUrl,
            webEdiInfo: localData.value.webEdiInfo,
            ediOrdersType: localData.value.ediOrdersType
        }
        // const itemSoc = generateSocieties(props.dataSociety)
        // await itemSoc.update(data)
        await fetchCustomerStore.update(data)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateIntegratedEdiFields() {
        const data = {
            isEdiAsn: localData.value.isEdiAsn,
            ediOrdersType: localData.value.ediOrdersType,
            ediOrdersMaturity: localData.value.ediOrdersMaturity
        }
        // const itemSoc = generateSocieties(props.dataSociety)
        // await itemSoc.update(data)
        await fetchCustomerStore.update(data)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
    function updateLocalWebEdiData(value) {
        localWebEdiData.value = value
    }
    function updateLocalIntegratedEdiData(value) {
        integratedEdiData.value
    }
    watch(() => localData.value, (newValue, oldValue) => {
        if (!newValue.isEdiOrders) {
            localData.value.ediKind = null
        }
    });
    function onCancelEdi(){
        localData.value = {
            isEdiOrders: props.dataCustomers.isEdiOrders,
            ediKind: props.dataCustomers.ediKind
        }
    }
    function onCancelWebEdi() {
        localWebEdiData.value = {
            webEdiUrl: props.dataCustomers.webEdiUrl,
            webEdiInfo: props.dataCustomers.webEdiInfo,
            ediOrdersType: props.dataCustomers.ediOrdersType,
        }
    }
    function onCancelIntegratedEdi() {
        integratedEdiData.value = {
            ediOrdersType: props.dataCustomers.ediOrdersType,
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
                id="addWebEdi"
                v-if="localData.isEdiOrders && localData.ediKind === EDI_WEB"
                class="ediCard"
                :enabled="isItAdmin"
                :fields="webEdiFields"
                :component-attribute="localWebEdiData"
                title="Paramètres Web Edi"
                @cancel="onCancelWebEdi"
                @update="updateWebEdiFields"
                @update:model-value="updateLocalWebEdiData"/>
            <AppCardShow
                id="addIntegratedEdi"
                v-if="localData.isEdiOrders && localData.ediKind === EDI_INTEGRATED"
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
