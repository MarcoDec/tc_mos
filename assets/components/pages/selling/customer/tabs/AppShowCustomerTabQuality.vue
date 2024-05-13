<script setup>
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import generateSocieties from '../../../../../stores/management/societies/societie'
    import {ref} from 'vue'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'

    const props = defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    })
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
    localData.value = {
        ppmRate: props.dataSociety.ppmRate
    }
    const qualityFields = [
        {label: 'Nb PPM', name: 'ppmRate', type: 'number'}
    ]

    const localQualityPortalData = ref({})
    localQualityPortalData.value = {
        getUrl: props.dataCustomers.qualityPortal.url,
        getUsername: props.dataCustomers.qualityPortal.username,
        getPassword: props.dataCustomers.qualityPortal.password
    }
    const qualityPortalFields = [
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
    ]
    async function updateQualityFields() {
        const data = {
            ppmRate: localData.value.ppmRate
        }
        const itemSoc = generateSocieties(props.dataSociety)
        await itemSoc.update(data)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateQualityPortalFields() {
        const dataQuality = {
            qualityPortal: {
                password: localData.value.getPassword,
                url: localData.value.getUrl,
                username: localData.value.getUsername
            }
        }
        const item = generateCustomer(props.dataCustomers)
        await item.updateQuality(dataQuality)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
    function updateLocalQualityPortalData(value) {
        localQualityPortalData.value = value
    }
</script>

<template>
    <div class="tabQuality">
        <AppCardShow
            id="addQualite"
            class="qualityCard"
            :fields="qualityFields"
            :component-attribute="localData"
            title="Paramètres Qualité"
            @update="updateQualityFields"
            @update:model-value="updateLocalData"/>
        <AppCardShow
            id="addPortalQualite"
            class="qualityCard"
            title="Portail Qualité"
            :fields="qualityPortalFields"
            :component-attribute="localQualityPortalData"
            @update="updateQualityPortalFields"
            @update:model-value="updateLocalQualityPortalData"/>
    </div>
</template>

<style scoped>
    div.tabQuality {
        display: flex;
        justify-content: center;
        flex-wrap: wrap
    }
    .qualityCard {
        min-width: 500px;
        margin-left: 20px;
        margin-bottom: 20px;
    }
</style>
