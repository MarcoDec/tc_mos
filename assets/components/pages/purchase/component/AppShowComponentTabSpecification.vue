<script setup>
    import AppCardShow from '../../../AppCardShow.vue'
    import {computed} from 'vue'
    import {useComponentListStore} from '../../../../stores/component/components'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const useFetchComponentStore = useComponentListStore()
    //await useFetchComponentStore.fetchOne(idComponent)
    const fecthOptions = useOptions('units')
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.text
            const optionList = {text, value}
            return optionList
        }))
    const Spécificationfields = [
        {label: 'Prix', measure: {code: 'Devise', value: 'valeur'}, name: 'price', type: 'measure'},
        {
            label: 'Poids Cuivre',
            name: 'copperWeight',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'measureselect'
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
        // const dataWeight = {
        //   copperWeight: {
        //     code: formData.get("copperWeight-code"),
        //     value: formData.get("copperWeight-value"),
        //   },
        // };

        await useFetchComponentStore.updatePrice(data, idComponent)
        await useFetchComponentStore.fetchOne(idComponent)
        // eslint-disable-next-line require-atomic-updates
        useFetchComponentStore.component.price.code = 'EUR'
    }
</script>

<template>
    <AppCardShow
        id="addSpécification"
        :fields="Spécificationfields"
        :component-attribute="useFetchComponentStore.component"
        @update="updateSpecification(useFetchComponentStore.component)"/>
</template>
