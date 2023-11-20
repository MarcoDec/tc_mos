<script setup>
    import {computed, ref, toRefs} from 'vue'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import {useIncotermStore} from '../../../../../stores/logistic/incoterm/incoterm'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'

    const {dataCustomers, dataSociety} = toRefs(defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    }))
    const fetchSocietyStore = useSocietyStore()
    const fetchCustomerStore = useCustomerStore()
    const localData = ref({})
    localData.value = {
        conveyanceDuration: {
            code: 'j',
            value: dataCustomers.value.conveyanceDuration.value
        },
        getPassword: dataCustomers.value.logisticPortal.password,
        getUrl: dataCustomers.value.logisticPortal.url,
        getUsername: dataCustomers.value.logisticPortal.username,
        incotermsValue: dataSociety.value.incoterms,
        nbDeliveries: dataCustomers.value.nbDeliveries,
        orderMin: {
            code: 'EUR',
            value: dataSociety.value.orderMin.value
        },
        outstandingMax: {
            code: 'EUR',
            value: dataCustomers.value.outstandingMax.value
        }
    }
    const fecthIncotermStore = useIncotermStore()
    await fecthIncotermStore.fetch()
    const optionsIncoterm = computed(() =>
        fecthIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            return {text, value}
        }))
    const logisticFields = computed(() => [
        {
            label: 'Nombre de bons de livraison mensuel ',
            name: 'nbDeliveries',
            type: 'number'
        },
        {label: 'DuréeTransport', measure: {code: 'j', value: 'valeur'}, name: 'conveyanceDuration', type: 'measure'},
        {label: 'Encours maximum', measure: {code: 'U', value: 'valeur'}, name: 'outstandingMax', type: 'measure'},
        {label: 'Commande minimum', measure: {code: 'U', value: 'valeur'}, name: 'orderMin', type: 'measure'},
        {
            label: 'Incoterm',
            name: 'incotermsValue',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
    ])
    async function updateLogistique() {
        //
        // const form = document.getElementById('addLogistique')
        // const formData = new FormData(form)
        const data = {
            conveyanceDuration: {
                code: 'j',
                value: parseFloat(localData.value.conveyanceDuration.value)
            },
            logisticPortal: {
                password: localData.value.getPassword,
                url: localData.value.getUrl,
                username: localData.value.getUsername
            },
            nbDeliveries: localData.value.nbDeliveries,
            outstandingMax: {
                code: 'EUR',
                value: parseFloat(localData.value.outstandingMax.value)
            }
        }
        const localDataSociety = {
            incoterms: localData.value.incotermsValue,
            orderMin: {
                code: 'EUR',
                value: parseFloat(localData.value.orderMin.value)
            }
        }

        const item = generateCustomer(dataCustomers.value)
        await item.updateLogistic(data)
        //await fetchCustomerStore.update(dataAccounting, customerId);

        //await item.update(data)
        await fetchSocietyStore.update(localDataSociety, dataSociety.value.id)
        // const itemSoc = generateSocieties(value)
        // await itemSoc.update(dataSociety)
        await fetchCustomerStore.fetchOne(dataCustomers.value.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
</script>

<template>
    <AppCardShow
        id="addLogistique"
        :fields="logisticFields"
        :component-attribute="localData"
        @update="updateLogistique"
        @update:model-value="updateLocalData"/>
</template>
