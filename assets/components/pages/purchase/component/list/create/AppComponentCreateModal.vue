<script setup>
    import {computed, onUnmounted, ref} from 'vue'
    import useAttributesStore from '../../../../../../stores/attribute/attributes'
    import {useColorsStore} from '../../../../../../stores/management/colors/colors'
    import {useComponentAttributesStore} from '../../../../../../stores/purchase/component/componentAttributesList'
    import useUnitsStore from '../../../../../../stores/unit/units'
    import AppModal from '../../../../../modal/AppModal.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppComponentCreate from './AppComponentCreate.vue'

    const props = defineProps({
        storeComponent: {required: true, type: Object}
    })
    const emits= defineEmits(['created'])
    const storeAttributes = useAttributesStore()
    const storeUnits = useUnitsStore()
    const storeColors = useColorsStore()
    const StoreComponentAttributes = useComponentAttributesStore()
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const fieldsAttributs = ref([])
    const familyValueChanged = ref(false)
    const inputAttributes = ref({})
    const listColors = ref([])
    const family = ref('')
    const attributesFiltered = ref([])
    const listUnits = ref([])
    const listAttributes = ref([])
    let fInput = {}
    let tabInput = []

    storeAttributes.getAttributes().then(() => {
        listAttributes.value = storeAttributes.listAttributes
        //tableKey.value += 1
    })
    storeUnits.getUnits().then(() => {
        listUnits.value = storeUnits.unitsOption
        //tableKey.value += 1
    })
    storeColors.getListColors().then(() => {
        listColors.value = storeColors.colorsOption
        //tableKey.value += 1
    })
    // await storeAttributes.getAttributes()
    // await storeUnits.getUnits()
    // await storeColors.getListColors()

    async function input(formInput) {
        fInput = computed(() => formInput)
        const oldFamily = family.value
        family.value = fInput.value.family
        familyValueChanged.value = !(oldFamily === family.value)
        if (familyValueChanged.value && typeof family.value !== 'undefined') {
            inputAttributes.value = {}
            attributesFiltered.value = listAttributes.value.filter(attribute => attribute.families.includes(family.value))
            fieldsAttributs.value = attributesFiltered.value.map(attribute => {
                if (attribute.type === 'color') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listColors.value.find(option => option.type === value)?.text ?? null,
                            options: listColors.value
                        },
                        type: 'select'
                    }
                }
                if (attribute.type === 'measureSelect') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listUnits.value.find(option => option.type === value)?.text ?? null,
                            options: listUnits.value
                        },
                        type: attribute.type
                    }
                }
                return {
                    name: attribute.name,
                    label: attribute.name,
                    type: attribute.type
                }
            })
        }
    }
    function inputAttribute(data) {
        inputAttributes.value = data
    }
    async function componentCreate() {
        const componentInput = {
            family: fInput.value.family,
            manufacturer: fInput.value.manufacturer,
            manufacturerCode: fInput.value.manufacturerCode,
            name: fInput.value.name,
            unit: fInput.value.unit,
            weight: fInput.value.weight
        }
        await props.storeComponent.addComponent(componentInput)
        tabInput = []
        for (const attribute in attributesFiltered.value) {
            const keys = Object.keys(inputAttributes.value.formInput)
            if (keys.includes(attributesFiltered.value[attribute].name)) {
                const inputAttribute = inputAttributes.value.formInput[attributesFiltered.value[attribute].name]
                if (attributesFiltered.value[attribute].type === 'color') {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        color: inputAttribute,
                        component: props.storeComponent.component['@id']
                    })
                } else if (attributesFiltered.value[attribute].type === 'measureSelect') {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        measure: inputAttribute,
                        component: props.storeComponent.component['@id']
                    })
                } else {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        value: inputAttribute,
                        component: props.storeComponent.component['@id']
                    })
                }
            } else {
                if (attributesFiltered.value[attribute].type === 'color') {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        color: '',
                        component: props.storeComponent.component['@id']
                    })
                } else if (attributesFiltered.value[attribute].type === 'measureSelect') {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        measure: {},
                        component: props.storeComponent.component['@id']
                    })
                } else {
                    tabInput.push({
                        attribute: attributesFiltered.value[attribute]['@id'],
                        value: '',
                        component: props.storeComponent.component['@id']
                    })
                }
            }
        }
        const promises = []
        for (const key in tabInput){
            promises.push(StoreComponentAttributes.addComponentAttributes(tabInput[key]))
        }
        Promise.all(promises).then(() => emits('created'))
    }

    onUnmounted(() => {
        // storeAttributes.reset()
        // storeUnits.reset()
        // storeColors.reset()
        // StoreComponentAttributes.reset()
    })
</script>

<template>
    <AppModal :id="modalId" class="four" title="Créer un Composant" size="xl">
        <AppSuspense>
            <AppComponentCreate :fields-attributs="fieldsAttributs" :my-boolean-family="familyValueChanged" @update:model-value="input" @data-attribute="inputAttribute"/>
        </AppSuspense>
        <template #buttons>
            <AppBtn
                variant="success"
                label="Créer"
                data-bs-toggle="modal"
                :data-bs-target="target"
                @click="componentCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>
