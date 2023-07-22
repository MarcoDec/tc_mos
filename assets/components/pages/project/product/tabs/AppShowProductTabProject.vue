<script setup>
    import {useRoute} from 'vue-router'
    import generateProduct from '../../../../../stores/product/product'
    import {ref} from 'vue'
    import {useProductStore} from '../../../../../stores/product/products'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchProductStore = useProductStore()
    const projectFields = [
        {label: 'Date Fin', name: 'getEndOfLife', type: 'date'},
        {label: 'maxProto', measure: {code: 'U', value: 'valeur'}, name: 'maxProto', type: 'measure'},
        {label: 'Prix (champ calculé)', measure: {code: 'Devise', value: 'valeur'}, name: 'price', type: 'measure'},
        //   { label: "Prix Cuivre", name: "priceWithoutCopper", type: "measure" },
        {
            label: 'Prix Transfer Fournisseur (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'transfertPriceSupplies',
            type: 'measure'
        },
        {
            label: 'Prix Transfer Work (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'transfertPriceWork',
            type: 'measure'
        }
    ]
    const projectPriceFields = [
        {
            label: 'Prix Cuivre (champ calculé)',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'priceWithoutCopper',
            type: 'measure'
        }
    ]
    async function updateProject(value) {
        const form = document.getElementById('addProject')
        const formData = new FormData(form)

        const data = {
            endOfLife: formData.get('getEndOfLife')
            // maxProto: {
            //   code: formData.get("maxProto-code"),
            //   value: JSON.parse(formData.get("maxProto-value")),
            // },
        }

        const item = generateProduct(value)
        await item.updateProject(data)
        await fetchProductStore.fetchOne(idProduct)
    }
    const managedCopperValue = ref(fetchProductStore.product.managedCopper)
</script>

<template>
    <div>
        <AppCardShow
            id="addProject"
            :fields="projectFields"
            :component-attribute="fetchProductStore.product"
            @update="updateProject(fetchProductStore.product)"/>
        <AppFormJS
            v-if="managedCopperValue"
            id="manager"
            :fields="projectPriceFields"
            :model-value="fetchProductStore.product"
            disabled/>
    </div>
</template>

