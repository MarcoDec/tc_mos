<script setup>
    import {computed, ref} from 'vue'
    import AppComponentCreate from './AppComponentCreate.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import AppTablePage from '../AppTablePage'
    import useAttributesStore from '../../../stores/attribute/attributes'
    import useColorsStore from '../../../stores/color/colors'
    import useComponentAttributesStore from '../../../stores/component/componentAttribute'
    import useComponentsStore from '../../../stores/component/components'
    import {useTableMachine} from '../../../machine'
    import useUnitsStore from '../../../stores/unit/units'

    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineComponet = useTableMachine('machine-component')
    const StoreComponents = useComponentsStore()
    const StoreComponentAttributes = useComponentAttributesStore()
    const storeAttributes = useAttributesStore()
    const storeUnits = useUnitsStore()
    const storeColors = useColorsStore()

    const fields = [
        {
            create: false,
            label: 'Img',
            name: 'img',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Référence',
            name: 'ref',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Indice',
            name: 'indice',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Désignation',
            name: 'designation',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Famille',
            name: 'famille',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Fournisseurs',
            name: 'fournisseurs',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Stocks',
            name: 'stocks',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Besoins enregistrés',
            name: 'besoin',
            sort: false,
            update: true
        },
        {
            create: false,
            label: 'Etat',
            name: 'etat',
            sort: false,
            type: 'trafficLight',
            update: true
        }
    ]
    let fInput = {}
    const family = ref('')
    const myBooleanFamily = ref(false)
    const fieldsAttributs = ref([])
    const attributesFiltered = ref([])
    const inputAttributes = ref({})

    async function input(formInput) {
        fInput = computed(() => formInput)
        const oldFamily = family.value
        family.value = fInput.value.family
        if (oldFamily === family.value) {
            myBooleanFamily.value = false
        } else {
            myBooleanFamily.value = true
        }
        if (family.value !== undefined) {
            inputAttributes.value = {}
            await storeAttributes.getAttributes()
            await storeUnits.getUnits()
            const listUnits = storeUnits.unitsOption
            await storeColors.getListColors()
            const listColors = storeColors.colorsOption

            attributesFiltered.value = storeAttributes.listAttributes.filter(attribute => attribute.families.includes(family.value))
            const newFields = attributesFiltered.value.map(attribute => {
                if (attribute.type === 'color') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listColors.find(option => option.type === value)?.text ?? null,
                            options: listColors
                        },
                        type: 'select'
                    }
                } if (attribute.type === 'measureSelect') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listUnits.find(option => option.type === value)?.text ?? null,
                            options: listUnits
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
            fieldsAttributs.value = newFields
        }
    }

    function inputAttribute(data) {
        inputAttributes.value = data
    }
    let tabInput = []
    async function Componentcreate() {
        const componentInput = {
            family: fInput.value.family,
            manufacturer: fInput.value.manufacturer,
            manufacturerCode: fInput.value.manufacturerCode,
            name: fInput.value.name,
            unit: fInput.value.unit,
            weight: fInput.value.weight
        }
        await StoreComponents.addComponent(componentInput)
        const newComponent = await StoreComponents.component
        tabInput = []
        for (const key in inputAttributes.value.formInput) {
            if (typeof inputAttributes.value.formInput[key] === 'object') {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    measure: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            } else if (key === 'couleur') {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    color: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            } else {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    value: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            }
        }
        for (const key in tabInput){
            await StoreComponentAttributes.addComponentAttributes(tabInput[key])
        }
    }
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title" size="xl">
            <AppSuspense>
                <AppComponentCreate :fields-attributs="fieldsAttributs" :my-boolean-family="myBooleanFamily" @update:model-value="input" @dataAttribute="inputAttribute"/>
            </AppSuspense>
            <template #buttons>
                <AppBtn
                    variant="success"
                    label="Créer"
                    data-bs-toggle="modal"
                    :data-bs-target="target"
                    @click="Componentcreate">
                    Créer
                </AppBtn>
            </template>
        </AppModal>

        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineComponet"
                :store="StoreComponents"
                title="La liste de composants">
                <template #cell(etat)>
                    <AppTrafficLight/>
                </template>
                <template #btn>
                    <AppBtn
                        variant="success"
                        label="créer"
                        data-bs-toggle="modal"
                        :data-bs-target="target">
                        Créer
                    </AppBtn>
                </template>
            </AppTablePage>
        </div>
    </div>
</template>
