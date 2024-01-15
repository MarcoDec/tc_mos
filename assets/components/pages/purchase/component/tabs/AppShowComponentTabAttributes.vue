<script setup>
    import {computed, onUnmounted, ref} from 'vue'
    //import generateComponentAttribute from '../../../../stores/component/componentAttribute'
    import useAttributes from '../../../../../stores/attribute/attributes'
    import {useColorsStore} from '../../../../../stores/management/colors/colors'
    import {useComponentAttributesStore} from '../../../../../stores/purchase/component/componentAttributesList'
    import useOptions from '../../../../../stores/option/options'
    import {useRoute} from 'vue-router'

    //region définition des constantes
    const route = useRoute()
    const fetchOptions = useOptions('units')
    const componentAttributesStore = useComponentAttributesStore()
    const attributesStore = useAttributes()
    const fecthColors = useColorsStore()
    const idComponent = route.params.id_component

    await fetchOptions.fetchOp()
    const optionsUnit = computed(() =>
        fetchOptions.options.map(op => {
            const text = op.text
            const value = op.text
            return {text, value}
        }))
    //console.log('componentAttributesStore', componentAttributesStore)
    await componentAttributesStore.fetchByComponentId(idComponent)
    await attributesStore.fetch()
    await fecthColors.fetch()
    const optionsColors = computed(() =>
        fecthColors.colors.map(op => {
            const text = op.name
            const value = op['@id']
            return {text, value}
        }))
    const attributsFields = []
    const localData = ref({})
    componentAttributesStore.componentAttributes.forEach(aComponentAttribute => {
        const attribute = aComponentAttribute.attribute
        const attributeObject = attributesStore.attributes.filter(anAttribute => anAttribute['@id'] === attribute)[0]
        const field = {
            label: attributeObject.name,
            name: `${aComponentAttribute['@id']}`,
            type: attributeObject.type
        }
        if (field.type === 'measureSelect') {
            field.options = {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            }
            localData.value[`${aComponentAttribute['@id']}`] = aComponentAttribute.measure
        }
        if (field.type === 'color') {
            field.options = {
                label: value =>
                    optionsColors.value.find(option => option.type === value)?.text ?? null,
                options: optionsColors.value
            }
            localData.value[`${aComponentAttribute['@id']}`] = aComponentAttribute.color
            field.type = 'select' //On force le select ici afin de ne référencer qu'une couleur officielle
        }
        if (field.type === 'text') {
            localData.value[`${aComponentAttribute['@id']}`] = aComponentAttribute.value
        }
        attributsFields.push(field)
    })
    function update(value) {
        localData.value = value
    }
    async function save() {
        Object.entries(localData.value).forEach(item => {
            const currAttr = attributsFields.filter(attr => attr.name === item[0])[0]
            if (currAttr.type === 'text') {
                componentAttributesStore.updateComponentAttributeValue(item[0], item[1])
            } else if (currAttr.type === 'measureSelect') {
                componentAttributesStore.updateComponentAttributeMeasure(item[0], item[1])
            } else {
                componentAttributesStore.updateComponentAttributeColor(item[0], item[1])
            }
        })
    }
    onUnmounted(() => {
        componentAttributesStore.reset()
        attributesStore.reset()
        fecthColors.reset()
        fetchOptions.resetItems()
    })
</script>

<template>
    <AppCardShow
        id="addAttribut"
        :fields="attributsFields"
        :component-attribute="localData"
        @update="save"
        @update:model-value="update"/>
</template>
