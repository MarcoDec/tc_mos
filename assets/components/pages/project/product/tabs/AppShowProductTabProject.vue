<script setup>
    import generateProduct from '../../../../../stores/product/product'
    import {useProductStore} from '../../../../../stores/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchProductStore = useProductStore()
    const projectFields = [
        {label: 'Date Fin', name: 'getEndOfLife', type: 'date'},
        {label: 'maxProto', measure: {code: 'U', value: 'valeur'}, name: 'maxProto', type: 'measure'}
    ]
    async function updateProject(value) {
        const form = document.getElementById('addProject')
        const formData = new FormData(form)

        const data = {
            endOfLife: formData.get('getEndOfLife'),
            maxProto: {
                code: formData.get('maxProto-code'),
                value: JSON.parse(formData.get('maxProto-value'))
            }
        }
        const item = generateProduct(value)
        await item.updateProject(data)
        await fetchProductStore.fetchOne(idProduct)
    }
</script>

<template>
    <div>
        <AppCardShow
            id="addProject"
            :fields="projectFields"
            :component-attribute="fetchProductStore.product"
            @update="updateProject(fetchProductStore.product)"/>
        <fieldset class="m-3 scheduler-border" name="prix" disabled>
            <legend data-v-c8d9e039="" class="scheduler-border">
                Prix (champs calculés)
            </legend>
            <div class="mb-3 row">
                <label
                    class="col-form-label col-md-3 col-xs-12"
                    for="manager-transfertPriceSupplies">
                    Prix Transfer Fournisseur
                </label>
                <div class="col">
                    <div id="manager-transfertPriceSupplies" class="input-group">
                        <input
                            id="manager-transfertPriceSupplies-value"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceSupplies-value"
                            :value="fetchProductStore.product.transfertPriceSupplies.value"
                            type="text"/>
                        <input
                            id="manager-transfertPriceSupplies-code"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceSupplies-code"
                            :value="fetchProductStore.product.transfertPriceSupplies.code"
                            type="text"/>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 col-xs-12" for="manager-transfertPriceWork">
                    Prix Transfer Work
                </label>
                <div class="col">
                    <div id="manager-transfertPriceWork" class="input-group">
                        <input
                            id="manager-transfertPriceWork-value"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceWork-value"
                            :value="fetchProductStore.product.transfertPriceWork.value"
                            type="text"/>
                        <input
                            id="manager-transfertPriceWork-code"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="transfertPriceWork-code"
                            :value="fetchProductStore.product.transfertPriceWork.code"
                            type="text"/>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 col-xs-12" for="manager-priceWithoutCopper">
                    Prix Sans Cuivre
                </label>
                <div class="col">
                    <div id="manager-priceWithoutCopper" class="input-group">
                        <input
                            id="manager-priceWithoutCopper-value"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="priceWithoutCopper-value"
                            :value="fetchProductStore.product.priceWithoutCopper.value"
                            type="text"/>
                        <input
                            id="manager-priceWithoutCopper-code"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="priceWithoutCopper-code"
                            :value="fetchProductStore.product.priceWithoutCopper.code"
                            type="text"/>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label
                    class="col-form-label col-md-3 col-xs-12"
                    for="manager-price">
                    Prix
                </label>
                <div class="col">
                    <div id="manager-price" class="input-group">
                        <input
                            id="manager-price-value"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="price-value"
                            :value="fetchProductStore.product.price.value"
                            type="text"/>
                        <input
                            id="manager-price-code"
                            autocomplete="off"
                            class="form-control form-control-sm"
                            form="manager"
                            name="price-code"
                            :value="fetchProductStore.product.price.code"
                            type="text"/>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</template>

