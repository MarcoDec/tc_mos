<script setup>
    import generateCustomer from '../../../../stores/customers/customer'
    import generateSocieties from '../../../../stores/societies/societie'
    import {ref} from 'vue'
    import {useCustomerStore} from '../../../../stores/customers/customers'

    const props = defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    })
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
    localData.value = {
        getPassword: props.dataCustomers.getPassword,
        getUrl: props.dataCustomers.getUrl,
        getUsername: props.dataCustomers.getUsername,
        ppmRate: props.dataSociety.ppmRate
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
        const item = generateCustomer(props.dataCustomers)
        await item.updateQuality(dataQuality)
        const itemSoc = generateSocieties(props.dataSociety)
        await itemSoc.update(data)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
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
