<script setup>
    import { computed, ref } from 'vue'
    import useAttributesStore from '../../../stores/attribute/attributes'
    import useComponentFamilyStore from '../../../stores/component/componentFamily'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import AppSuspense from '../../../components/AppSuspense.vue'

    const storeAttributes = useAttributesStore()
    const storeComponentFamilly = useComponentFamilyStore()

    await storeAttributes.getAttributes()
    await storeComponentFamilly.getComponentFamily()
    const listFamilies = storeComponentFamilly.familiesOption
    console.log('listFamilies', listFamilies)
    console.log('Attributes', storeAttributes.listAttributes)

    const fieldsForm = [
        {
            label: 'Famille',
            name: 'family',
            options: {
                label: value => listFamilies.find(option => option.type === value)?.text ?? null,
                options: listFamilies
            },
            type: 'select'
        }
    ]

    let formData = {}
    let formInput= {}
    const fields = ref([])

    async function filteredAttributes(formData) {
        console.log('formData', formData)
        const attributesFiltered = storeAttributes.listAttributes.filter(attribute => attribute.families.includes(formData.family))
        console.log('attributesFiltered', attributesFiltered)

        const newFields = attributesFiltered.map(attribute => ({
            name: attribute.name,
            label: attribute.name,
            type: attribute.type
        }))
        fields.value = newFields
    }

    console.log('formInput', computed(() =>formInput))
</script>

<template>
    <AppSuspense>
        <AppFormJS id="addFamilly" :fields="fieldsForm" v-model="formData" @click="filteredAttributes(formData)"/>
        <AppFormJS id="addAttributes" v-if="fields.length !== 0" :fields="fields" v-model="formInput"/>
    </AppSuspense>
</template>

