<script setup>
    import {computed, ref} from 'vue'
    import generateProduct from '../../../../../stores/project/product/product'
    import {useIncotermStore} from '../../../../../stores/logistic/incoterm/incoterm'
    import useOptions from '../../../../../stores/option/options'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import AppCardShow from '../../../../AppCardShow.vue'

    const isError2 = ref(false)
    const violations2 = ref([])
    const fetchProductStore = useProductStore()
    const fetchIncotermStore = useIncotermStore()
    await fetchIncotermStore.fetch()
    const fecthOptions = useOptions('units')
    await fecthOptions.fetchOp()
    const optionsIncoterm = computed(() =>
        fetchIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            return {text, value}
        }))
    const optionsUnitText = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            return {text, value}
        }))
    const Logistiquefields = [
        {label: 'Code douanier (10 caractères max)', name: 'customsCode', type: 'text'},
        {
            label: 'Incoterms',
            name: 'incoterms',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {
            label: 'Stock Min',
            measure: {
                code: {
                    label: 'unité',
                    name: 'code',
                    type: 'text'
                },
                value: {
                    label: 'valeur',
                    name: 'value',
                    type: 'number',
                    step: 0.01
                }
            },
            name: 'minStock',
            type: 'measure'
        },
        {
            label: 'Delivery Min',
            measure: {
                code: {
                    label: 'unité',
                    name: 'code',
                    type: 'text'
                },
                value: {
                    label: 'valeur',
                    name: 'value',
                    type: 'number',
                    step: 0.01
                }
            },
            name: 'minDelivery',
            type: 'measure'
        },
        {
            label: 'Poids',
            name: 'weight',
            options: {
                label: value =>
                    optionsUnitText.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsUnitText.value
            },
            type: 'measureSelect'
        }
    ]
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            customsCode: formData.get('customsCode'),
            incoterms: formData.get('incoterms'),
            minDelivery: {
                code: formData.get('minDelivery-code'),
                value: JSON.parse(formData.get('minDelivery-value'))
            },
            minStock: {
                code: formData.get('minStock-code'),
                value: JSON.parse(formData.get('minStock-value'))
            },

            weight: {
                code: formData.get('weight-code'),
                value: JSON.parse(formData.get('weight-value'))
            }
        }
        try {
            const item = generateProduct(value)
            await item.updateLogistique(data)
            await fetchProductStore.fetchOne(idProduct)
        } catch (error) {
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
</script>

<template>
    <div>
        <AppCardShow
            id="addLogistique"
            :fields="Logistiquefields"
            :component-attribute="fetchProductStore.product"
            title="Logistique"
            @update="updateLogistique(fetchProductStore.product)"/>
        <div v-if="isError2" class="alert alert-danger" role="alert">
            <div v-for="violation in violations2" :key="violation">
                <li>{{ violation.propertyPath }} {{ violation.message }}</li>
            </div>
        </div>
    </div>
</template>

