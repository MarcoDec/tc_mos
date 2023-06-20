<script setup>
    import generateComponentAttribute from '../../../../stores/component/componentAttribute'
    import {useComponentShowStore} from '../../../../stores/component/componentAttributesList'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component

    const useComponentStore = useComponentShowStore()
    await useComponentStore.fetchOne(idComponent)
    // const fecthColors = useColorsStore()
    // await fecthColors.fetch()
    //
    // const optionsColors = computed(() =>
    //     fecthColors.colors.map(op => {
    //         const text = op.name
    //         const value = op.id
    //         const optionList = {text, value}
    //         return optionList
    //     }))
    const Attributfields = [
        // {
        //     label: 'Couleur',
        //     name: 'getColor',
        //     options: {
        //         label: value =>
        //             optionsColors.value.find(option => option.type === value)?.text
        //             ?? null,
        //         options: optionsColors.value
        //     },
        //     type: 'select'
        // },
        {label: 'Attribute', name: 'attribute', type: 'text'},
        {label: 'Valeur', name: 'value', type: 'text'}
        // {label: 'Voltage (V)', name: 'Voltage', type: 'text'},
        // {label: 'Dia ext maxi (mm)', name: 'DiaMaxi', type: 'number'},
        // {label: 'Norme d\'appellation', name: 'Norme', type: 'text'},
        // {
        //     label: 'Nombre des conducteurs',
        //     name: 'NombreConducteurs',
        //     type: 'number'
        // },
        // {label: 'Section (mm²)', name: 'MatièreBrins', type: 'text'},
        // {label: 'T° mini (°C)', name: 'temperatureMini', type: 'number'},
        // {label: 'Matière d\'isolant', name: 'MatièreIsolant', type: 'text'},
        // {label: 'needJoint', name: 'needJoint', type: 'boolean'}
    ]
    async function update(value) {
        const form = document.getElementById('addAttribut')
        const formData = new FormData(form)
        const data = {
            color: `/api/colors/${formData.get('getColor')}`,
            measure: {
                code: 'U',
                value: 1
            },
            value: 'string'
        }
        const item = generateComponentAttribute(value)

        await item.updateAttributes(data)

        await useComponentStore.fetchOne(idComponent)
    }
</script>

<template>
    <AppCardShow
        v-for="item in useComponentStore.componentAttribute"
        id="addAttribut"
        :key="item.id"
        :fields="Attributfields"
        :component-attribute="item"
        @update="update(item)"/>
</template>
