<script setup>
    import generateSocieties from '../../../../../stores/management/societies/societie'
    import generateSupplier from '../../../../../stores/purchase/supplier/supplier'
    import {ref} from 'vue'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'

    // const props = defineProps({
    //     componentAttribute: {required: true, type: Object}
    // })
    const emit = defineEmits(['update:modelValue'])
    const qualityFields = [
        {label: 'Gestion de la qualité', name: 'managedQuality', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'confidenceCriteria', type: 'rating'},
        {label: 'Taux de PPM', name: 'ppmRate', type: 'number'}
    ]
    const fetchSuppliersStore = useSuppliersStore()
    const fetchSocietyStore = useSocietyStore()

    const localData = ref({
        confidenceCriteria: Number(fetchSuppliersStore.supplier.confidenceCriteria),
        idSociety: fetchSocietyStore.society.id,
        idSupplier: fetchSuppliersStore.supplier.id,
        managedQuality: fetchSuppliersStore.supplier.managedQuality,
        ppmRate: Number(fetchSocietyStore.society.ppmRate)
    })
    async function update() {
        const data = {
            confidenceCriteria: localData.value.confidenceCriteria,
            id: localData.value.idSupplier,
            managedQuality: localData.value.managedQuality
        }
        const dataSociety = {
            id: localData.value.idSociety,
            ppmRate: localData.value.ppmRate
        }
        const item = generateSupplier(data)
        const itemSoc = generateSocieties(dataSociety)
        await itemSoc.update({ppmRate: dataSociety.ppmRate})
        await item.updateQuality({
            confidenceCriteria: data.confidenceCriteria,
            managedQuality: data.managedQuality
        })
    }
    async function input(value) {
        localData.value = {...localData.value, ...value}
        emit('update:modelValue', localData.value)
    }
</script>

<template>
    <AppCardShow
        id="addQualite"
        :fields="qualityFields"
        :component-attribute="localData"
        @update="update"
        @update:model-value="input"/>
</template>
