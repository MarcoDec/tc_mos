<script setup>
    import {computed, ref} from 'vue'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import {useIncotermStore} from '../../../../../stores/logistic/incoterm/incoterm'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'

    const props = defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    })
    const fetchSocietyStore = useSocietyStore()
    const fetchCustomerStore = useCustomerStore()
    const localLogisticPortalData = ref({})
    localLogisticPortalData.value = {
        getPassword: props.dataCustomers.logisticPortal.password,
        getUrl: props.dataCustomers.logisticPortal.url,
        getUsername: props.dataCustomers.logisticPortal.username
    }
    const localData = ref({})
    localData.value = {
        conveyanceDuration: {
            code: 'j',
            value: props.dataCustomers.conveyanceDuration.value
        },
        incotermsValue: props.dataSociety.incoterms,
        nbDeliveries: props.dataCustomers.nbDeliveries,
        orderMin: {
            code: 'EUR',
            value: props.dataSociety.orderMin.value
        },
        outstandingMax: {
            code: 'EUR',
            value: props.dataCustomers.outstandingMax.value
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
    const logisticPortalFields = computed(() => [
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
    ])
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
        }
    ])
    async function updatePortalLogistique() {
        const data = {
            logisticPortal: {
                password: localData.value.getPassword,
                url: localData.value.getUrl,
                username: localData.value.getUsername
            },
        }
        const item = generateCustomer(props.dataCustomers)
        await item.updateLogistic(data)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateLogistique() {
        //
        // const form = document.getElementById('addLogistique')
        // const formData = new FormData(form)
        const data = {
            conveyanceDuration: {
                code: 'j',
                value: parseFloat(localData.value.conveyanceDuration.value)
            },
            nbDeliveries: localData.value.nbDeliveries,
            outstandingMax: {
                code: 'EUR',
                value: parseFloat(localData.value.outstandingMax.value)
            }
        }
        const dataSociety = {
            incoterms: localData.value.incotermsValue,
            orderMin: {
                code: 'EUR',
                value: parseFloat(localData.value.orderMin.value)
            }
        }

        const item = generateCustomer(props.dataCustomers)
        await item.updateLogistic(data)
        //await fetchCustomerStore.update(dataAccounting, customerId);

        //await item.update(data)
        await fetchSocietyStore.update(dataSociety, props.dataSociety.id)
        // const itemSoc = generateSocieties(value)
        // await itemSoc.update(dataSociety)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    function updateLocalPortalData(value) {
        localLogisticPortalData.value = value
    }
    function updateLocalData(value) {
        localData.value = value
    }
</script>

<template>
    <div class="logisticTab">
        <AppCardShow
            id="addLogistique"
            class="logiticForm"
            :fields="logisticFields"
            :component-attribute="localData"
            title="Paramètres Logistiques"
            @update="updateLogistique"
            @update:model-value="updateLocalData"/>
        <AppCardShow
            id="addLogistiquePortal"
            class="logiticForm"
            :fields="logisticPortalFields"
            :component-attribute="localLogisticPortalData"
            title="Portail Logistique"
            @update="updatePortalLogistique"
            @update:model-value="updateLocalPortalData"/>
    </div>
</template>

<style scoped>
    div.logisticTab {
        display: flex;
        justify-content: center;
        flex-wrap: wrap
    }
    .logiticForm {
        min-width: 500px;
        margin-left: 20px;
        margin-bottom: 20px;
    }
</style>