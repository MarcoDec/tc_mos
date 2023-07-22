<script setup>
    import generateProduct from '../../../../../stores/product/product'
    import {useProductStore} from '../../../../../stores/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)

    const fetchProductStore = useProductStore()
    const productionFields = [
        {label: 'Duration Auto', measure: {code: 'h', value: 'valeur'}, name: 'autoDuration', type: 'measure'},
        {label: 'Duration Manual', measure: {code: 'h', value: 'valeur'}, name: 'manualDuration', type: 'measure'},
        {label: 'Production Min', measure: {code: 'U', value: 'valeur'}, name: 'minProd', type: 'measure'},
        {label: 'Production Delay', measure: {code: 'jr', value: 'valeur'}, name: 'productionDelay', type: 'measure'},
        {
            label: 'Volume prévisionnel (champ calculé)',
            measure: {code: 'U', value: 'valeur'},
            name: 'forecastVolume',
            type: 'measure'
        },
        {label: 'Packaging', measure: {code: 'Devise', value: 'valeur'}, name: 'packaging', type: 'measure'},
        {label: 'Packaging Kind', name: 'packagingKind', type: 'text'}
    ]
    async function updateProduction(value) {
        const form = document.getElementById('addProduction')
        const formData = new FormData(form)

        const data = {
            autoDuration: {
                //code: formData.get("autoDuration-code"),
                value: JSON.parse(formData.get('autoDuration-value'))
            },
            // forecastVolume: {
            //   code: formData.get("forecastVolume-code"),
            //   value: JSON.parse(formData.get("forecastVolume-value")),
            // },
            manualDuration: {
                //code: formData.get("manualDuration-code"),
                value: JSON.parse(formData.get('manualDuration-value'))
            },
            minProd: {
                //code: formData.get("minProd-code"),
                value: JSON.parse(formData.get('minProd-value'))
            },
            packaging: {
                //code: formData.get("packaging-code"),
                value: JSON.parse(formData.get('packaging-value'))
            },
            packagingKind: formData.get('packagingKind'),
            productionDelay: {
                //code: formData.get("productionDelay-code"),
                value: JSON.parse(formData.get('productionDelay-value'))
            }
        }

        const item = generateProduct(value)
        await item.updateProduction(data)
        await fetchProductStore.fetchOne(idProduct)
    }
</script>

<template>
    <AppCardShow
        id="addProduction"
        :fields="productionFields"
        :component-attribute="fetchProductStore.product"
        @update="updateProduction(fetchProductStore.product)"/>
</template>

