<script setup>
    import generateProduct from '../../../../../stores/project/product/product'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)

    const fetchProductStore = useProductStore()
    const productionFields = [
        {label: 'Duration Auto', measure: {code: 'h', value: 'valeur'}, name: 'autoDuration', type: 'measure'},
        {label: 'Duration Manual', measure: {code: 'h', value: 'valeur'}, name: 'manualDuration', type: 'measure'},
        {label: 'Production Min', measure: {code: 'U', value: 'valeur'}, name: 'minProd', type: 'measure'},
        {label: 'Production Delay', measure: {code: 'j', value: 'valeur'}, name: 'productionDelay', type: 'measure'},
        // {
        //     label: 'Volume prévisionnel (champ calculé)',
        //     measure: {code: 'U', value: 'valeur'},
        //     name: 'forecastVolume',
        //     type: 'measure'
        // },
        {label: 'Packaging', measure: {code: 'Devise', value: 'valeur'}, name: 'packaging', type: 'measure'},
        {label: 'Packaging Kind', name: 'packagingKind', type: 'text'}
    ]
    async function updateProduction(value) {
        const form = document.getElementById('addProduction')
        const formData = new FormData(form)

        const data = {
            autoDuration: {
                code: 'h',
                value: JSON.parse(formData.get('autoDuration-value'))
            },
            // forecastVolume: {
            //     code: formData.get('forecastVolume-code'),
            //     value: JSON.parse(formData.get('forecastVolume-value'))
            // },
            manualDuration: {
                code: 'h',
                value: JSON.parse(formData.get('manualDuration-value'))
            },
            minProd: {
                code: 'U',
                value: JSON.parse(formData.get('minProd-value'))
            },
            packaging: {
                code: 'U',
                value: JSON.parse(formData.get('packaging-value'))
            },
            packagingKind: formData.get('packagingKind'),
            productionDelay: {
                code: 'j',
                value: JSON.parse(formData.get('productionDelay-value'))
            }
        }

        const item = generateProduct(value)
        await item.updateProduction(data)
        await fetchProductStore.fetchOne(idProduct)
    }
</script>

<template>
    <div>
        <fieldset class="bg-light m-3 scheduler-border text-info" name="volume" disabled>
            <legend data-v-c8d9e039="" class="scheduler-border">
                Champs calculés
            </legend>
            <div class="mb-3 row">
                <label
                    class="col-form-label col-md-3 col-xs-12"
                    for="manager-forecastedVolume">
                    Volume prévisionnel
                </label>
                <div class="col">
                    <div id="manager-forecastedVolume" class="input-group">
                        <input
                            id="manager-forecastedVolume-value"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceSupplies-value"
                            :value="fetchProductStore.product.forecastVolume.value"
                            type="text"/>
                        <input
                            id="manager-forecastedVolume-code"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceSupplies-code"
                            :value="fetchProductStore.product.forecastVolume.code"
                            type="text"/>
                    </div>
                </div>
            </div>
        </fieldset>
        <AppCardShow
            id="addProduction"
            :fields="productionFields"
            :component-attribute="fetchProductStore.product"
            @update="updateProduction(fetchProductStore.product)"/>
    </div>
</template>

