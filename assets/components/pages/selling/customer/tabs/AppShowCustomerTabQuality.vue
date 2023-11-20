<script setup>
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import generateSocieties from '../../../../../stores/management/societies/societie'
    import {ref, toRefs} from 'vue'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'

    const {dataCustomers, dataSociety} = toRefs(defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    }))
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
    localData.value = {
        getPassword: dataCustomers.value.qualityPortal.password,
        getUrl: dataCustomers.value.qualityPortal.url,
        getUsername: dataCustomers.value.qualityPortal.username,
        ppmRate: dataSociety.value.ppmRate
    }
    const qualityFields = [
        {label: 'Nb PPM', name: 'ppmRate', type: 'number'},
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
    ]
    async function updateQualityFields() {
        const data = {
            ppmRate: localData.value.ppmRate
        }
        const dataQuality = {
            qualityPortal: {
                password: localData.value.getPassword,
                url: localData.value.getUrl,
                username: localData.value.getUsername
            }
        }
        const item = generateCustomer(dataCustomers.value)
        await item.updateQuality(dataQuality)
        const itemSoc = generateSocieties(dataSociety.value)
        await itemSoc.update(data)
        await fetchCustomerStore.fetchOne(dataCustomers.value.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
</script>

<template>
    <AppCardShow
        id="addQualite"
        :fields="qualityFields"
        :component-attribute="localData"
        @update="updateQualityFields"
        @update:model-value="updateLocalData"/>
</template>
