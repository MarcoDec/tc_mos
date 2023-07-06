<script setup>
    import generateCustomer from '../../../../stores/customers/customer'
    import generateSocieties from '../../../../stores/societies/societie'

    /*const props = */defineProps({
        dataCustomers: {required: true, type: Object}
    })
    const qualityFields = [
        {label: 'Nb PPM', name: 'ppmRate', type: 'number'},
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
    ]
    async function updateQte(value) {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {ppmRate: JSON.parse(formData.get('ppmRate'))}
        const dataAccounting = {
            accountingPortal: {
                password: formData.get('getPassword'),
                url: formData.get('getUrl'),
                username: formData.get('getUsername')
            }
        }

        const item = generateCustomer(value)
        await item.updateAccounting(dataAccounting)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(data)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
</script>

<template>
    <AppCardShow
        id="addQualite"
        :fields="qualityFields"
        :component-attribute="dataCustomers"
        @update="updateQte(dataCustomers)"/>
</template>
