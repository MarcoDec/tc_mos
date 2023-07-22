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
    //await fecthOptions.fetchOp()
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            return {text, value}
        }))
    const specificationFields = [
        {disabled: true, label: 'Prix', measure: {code: 'Devise', value: 'valeur'}, name: 'price', type: 'measure'},
        {
            label: 'Poids Cuivre',
            name: 'copperWeight',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureSelect'
        },
        {label: 'Info Cmde', name: 'orderInfo', type: 'text'}
    ]
    async function updateSpecification() {
        const form = document.getElementById('addSpécification')
        const formData = new FormData(form)

        const data = {
            copperWeight: {
                code: formData.get('copperWeight-code'),
                value: formData.get('copperWeight-value')
            },
            orderInfo: formData.get('orderInfo')
        }
        await useFetchComponentStore.updatePrice(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
        //useFetchComponentStore.component.price.code = 'EUR'
    }
</script>

<template>
    <AppCardShow
        id="addSpécification"
        :fields="specificationFields"
        :component-attribute="useFetchComponentStore.component"
        @update="updateSpecification(useFetchComponentStore.component)"/>
</template>
