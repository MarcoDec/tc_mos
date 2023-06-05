<script setup>
    import AppComponentCreate from './AppComponentCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useComponentsStore from '../../../stores/component/components'
    import {useTableMachine} from '../../../machine'
    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineComponet = useTableMachine('machine-component')
    const StoreComponents = useComponentsStore()
    // console.log('component', component)

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
    function input(formInput) {
        console.log('formInput', formInput)
        fInput = computed(() => formInput)
    }
    async function Componentcreate() {
        const componentInput = {
            family: fInput.value.family,
            manufacturer: fInput.value.manufacturer,
            manufacturerCode: fInput.value.manufacturerCode,
            name: fInput.value.name,
            unit: fInput.value.unit,
            weight: fInput.value.weight
        }
        console.log('componentInput', componentInput)
        await StoreComponents.addComponent(componentInput)
    }
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title" size="xl">
            <AppComponentCreate @update:model-value="input"/>
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
