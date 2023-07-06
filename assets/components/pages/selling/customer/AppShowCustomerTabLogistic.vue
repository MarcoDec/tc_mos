<script setup>
    import {computed} from 'vue'
    import generateCustomer from '../../../../stores/customers/customer'
    import {useIncotermStore} from '../../../../stores/incoterm/incoterm'

    /*const props = */defineProps({
        dataCustomers: {required: true, type: Object}
    })
    const fecthIncotermStore = useIncotermStore()
    await fecthIncotermStore.fetch()
    const optionsIncoterm = computed(() =>
        fecthIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            const optionList = {text, value}
            return optionList
        }))
    const Logistiquefields = computed(() => [
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
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            conveyanceDuration: {
                code: 'j',
                value: JSON.parse(formData.get('conveyanceDuration-value'))
            },
            nbDeliveries: JSON.parse(formData.get('nbDeliveries')),
            outstandingMax: {
                code: 'EUR',
                value: JSON.parse(formData.get('outstandingMax-value'))
            }
        }
        const dataSociety = {
            incoterms: formData.get('incotermsValue'),
            orderMin: {
                code: 'EUR',
                value: JSON.parse(formData.get('orderMin-value'))
            }
        }
        const dataAccounting = {
            accountingPortal: {
                password: formData.get('getPassword'),
                url: formData.get('getUrl'),
                username: formData.get('getUsername')
            }
        }

        const item = generateCustomer(value)
        await item.updateAccounting(dataAccounting)
        //await fetchCustomerStore.update(dataAccounting, customerId);

        await item.update(data)
        await fetchSocietyStore.update(dataSociety, societyId)
        // const itemSoc = generateSocieties(value)
        // await itemSoc.update(dataSociety)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
</script>

<template>
    <AppCardShow
        id="addLogistique"
        :fields="Logistiquefields"
        :component-attribute="dataCustomers"
        @update="updateLogistique(dataCustomers)"/>
</template>
