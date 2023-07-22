<script setup>
    import AppCardShow from '../../../../AppCardShow.vue'
    import {computed} from 'vue'
    import {useComponentListStore} from '../../../../../stores/component/components'
    import useOptions from '../../../../../stores/option/options'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const useFetchComponentStore = useComponentListStore()
    const fecthOptions = useOptions('units')
    //await useFetchComponentStore.fetchOne(idComponent)
    //await fecthOptions.fetchOp()
    const optionsAtt = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            return {text, value}
        }))
    const Logistiquefields = [
        {label: 'Code douanier', name: 'customsCode', type: 'text'},
        {
            label: 'Poids',
            name: 'weight',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsAtt.value.find(option => option.type === value)?.text ?? null,
                options: optionsAtt.value
            },
            type: 'select'
        },
        {
            label: 'Volume Prévisionnel',
            name: 'forecastVolume',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Stock Minimum',
            name: 'minStock',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureSelect'
        },
        {label: 'gestionStock', name: 'managedStock', type: 'boolean'}
    ]

    function updateLogistique() {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)
        const data = {
            customsCode: formData.get('customsCode'),
            family: '/api/component-families/4',
            forecastVolume: {
                code: formData.get('forecastVolume-code'),
                value: formData.get('forecastVolume-value')
            },
            managedStock: JSON.parse(formData.get('managedStock')),

            minStock: {
                code: formData.get('minStock-code'),
                value: formData.get('minStock-value')
            },
            orderInfo: formData.get('orderInfo'),
            unit: formData.get('unit'),
            weight: {
                code: formData.get('weight-code'),
                value: formData.get('weight-value')
            }
        }

        useFetchComponentStore.update(data, idComponent)
        useFetchComponentStore.fetchOne(idComponent)
    }
</script>

<template>
    <AppCardShow
        id="addLogistique"
        :fields="Logistiquefields"
        :component-attribute="useFetchComponentStore.component"
        @update="updateLogistique(useFetchComponentStore.component)"/>
</template>

