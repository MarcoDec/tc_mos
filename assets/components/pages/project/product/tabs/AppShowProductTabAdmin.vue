<script setup>
    import {computed} from 'vue'
    import generateProduct from '../../../../../stores/product/product'
    import useOptions from '../../../../../stores/option/options'
    import {useProductStore} from '../../../../../stores/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const fecthOptions = useOptions('units')
    await fecthOptions.fetchOp()
    const idProduct = Number(route.params.id_product)
    const fetchProductStore = useProductStore()
    const optionsKind = [
        {
            text: 'Série',
            value: 'Série'
        },
        {text: 'Prototype', value: 'Prototype'},
        {text: 'EI', value: 'EI'},
        {text: 'Pièce de rechange', value: 'Pièce de rechange'}
    ]
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const adminFields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Code', name: 'code', type: 'text'},
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'select'
        },
        {label: 'Index', name: 'index', type: 'text'},
        {label: 'Index interne', name: 'internalIndex', type: 'text'},
        {
            label: 'Kind',
            name: 'kind',
            options: {
                label: value =>
                    optionsKind.find(option => option.type === value)?.text ?? null,
                options: optionsKind
            },
            type: 'select'
        }
    ]
    async function updateAdmin(value) {
        const form = document.getElementById('addAdmin')
        const formData = new FormData(form)
        const data = {
            code: formData.get('code'),
            index: formData.get('index'),
            // internalIndex: formData.get("internalIndex"),
            kind: formData.get('kind'),
            name: formData.get('name')
        }

        const item = generateProduct(value)
        await item.updateAdmin(data)
        await fetchProductStore.fetchOne(idProduct)
    }
</script>

<template>
    <AppCardShow
        id="addAdmin"
        :fields="adminFields"
        :component-attribute="fetchProductStore.product"
        @update="updateAdmin(fetchProductStore.product)"/>
</template>

