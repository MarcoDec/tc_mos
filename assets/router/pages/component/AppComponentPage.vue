<script setup>
    import {ref} from 'vue'
    import AppComponentCreate from './AppComponentCreate.vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useComponentsStore from '../../../stores/component/components'
    import useComponentAttributesStore from '../../../stores/component/componentAttribute'
    import useAttributesStore from '../../../stores/attribute/attributes'
    import useUnitsStore from '../../../stores/unit/units'
    import {useTableMachine} from '../../../machine'
    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineComponet = useTableMachine('machine-component')
    const StoreComponents = useComponentsStore()
    const StoreComponentAttributes = useComponentAttributesStore()
    const storeAttributes = useAttributesStore()
    const storeUnits = useUnitsStore()

    
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
            update: true,
            type: 'trafficLight'
        }
    ]
    let fInput = {}
    const family = ref('') 
    const myBooleanFamily = ref(false);
    const fieldsAttributs = ref([])
    const attributesFiltered = ref([])
    const inputAttributes = ref({})
    


    async function input(formInput) {
        fInput = computed(() => formInput)
        console.log('fInput', fInput);
        const oldFamily = family.value;
        console.log('oldFamily', oldFamily);
        family.value = fInput.value.family
        if (oldFamily === family.value) {
            myBooleanFamily.value = false 
            console.log('myBooleanFamily', myBooleanFamily);
        }else{
            myBooleanFamily.value = true 
            console.log('myBooleanFamily', myBooleanFamily);
        }

        console.log('family', family);
        if (family!== undefined) {
            inputAttributes.value= {}
            console.log('===>', inputAttributes.value);
            await storeAttributes.getAttributes()
            console.log('Attributes', storeAttributes.listAttributes)
            await storeUnits.getUnits()
            const listUnits = storeUnits.unitsOption

            attributesFiltered.value = storeAttributes.listAttributes.filter(attribute => attribute.families.includes(family.value))
            console.log('attributesFiltered', attributesFiltered.value)
            const newFields = attributesFiltered.value.map(attribute => {
                console.log('attribute',attribute);
                if (attribute.type === 'measureSelect') {
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
                } else {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        type: attribute.type
                    }
        }
        })
        fieldsAttributs.value = newFields
        console.log('fieldsAttributs',fieldsAttributs.value);
    }
    }
    
    function inputAttribute(data) {
        // inputAttributes.value= {}
        console.log('data',data);
        inputAttributes.value= data
        console.log('inputAttributes', inputAttributes);
    }
    let tabInput = [];
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
        for (let key in inputAttributes.formInput) {
            if (typeof inputAttributes.formInput[key] === 'object') {
                const attribute = inputAttributes.attribute.find(item => item.name === key);
                tabInput.push({
                    attribute: attribute['@id'],
                    measure: inputAttributes.formInput[key],
                    component: newComponent['@id'],
                });
            }else if (key === 'couleur') {
                const attribute = inputAttributes.attribute.find(item => item.name === key);
                tabInput.push({
                    attribute: attribute['@id'],
                    color: inputAttributes.formInput[key],
                    component: newComponent['@id'],
                });
            }else{
                const attribute = inputAttributes.attribute.find(item => item.name === key);
                tabInput.push({
                    attribute: attribute['@id'],
                    value: inputAttributes.formInput[key],
                    component: newComponent['@id'],
                });
            }
        }
        console.log('tabInput', tabInput);
        for(let key in tabInput){
            await StoreComponentAttributes.addComponentAttributes(tabInput[key])
        }
    }
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title" size="xl">
            <AppSuspense>
                <AppComponentCreate :fieldsAttributs="fieldsAttributs" :myBooleanFamily="myBooleanFamily" @update:model-value="input" @dataAttribute="inputAttribute"/>
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
