<script setup>
    import {computed} from 'vue'
    import generateProduct from '../../../../../stores/product/product'
    import {useProductStore} from '../../../../../stores/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = route.params.id_product
    const fetchProductStore = useProductStore()
    await fetchProductStore.fetchProductFamily()
    const optionsProductFamily = computed(() =>
        fetchProductStore.productsFamily.map(op => {
            const text = op.fullName
            const value = op['@id']
            return {text, value}
        }))
    const generalFields = [
        {
            label: 'Famille',
            name: 'family',
            options: {
                label: value =>
                    optionsProductFamily.value.find(option => option.type === value)
                        ?.text ?? null,
                options: optionsProductFamily.value
            },
            type: 'select'
        },

        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Gestion du cuivre', name: 'managedCopper', type: 'boolean'}
    ]
    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)

        const data = {
            family: formData.get('family'),
            notes: formData.get('notes')
            //managedCopper: JSON.parse(formData.get("managedCopper")),
        }

        const item = generateProduct(value)
        await item.updateMain(data)
        await fetchProductStore.fetchOne(idProduct)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="generalFields"
        :component-attribute="fetchProductStore.product"
        @update="updateGeneral(fetchProductStore.product)"/>
</template>

