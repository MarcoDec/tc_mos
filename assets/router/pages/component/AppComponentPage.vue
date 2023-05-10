<script setup>
    import AppComponentCreate from './AppComponentCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useComponent from '../../../stores/component/components'
    import {useTableMachine} from '../../../machine'
    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    console.log('modalId', modalId)
    const target = computed(() => `#${modalId.value}`)
    const machineComponet = useTableMachine('machine-component')
    const component = useComponent()
    component.fetch()
    console.log('component', component)

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
            update: true
        }
    ]
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title">
            <AppComponentCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineComponet"
                :store="component"
                title="La liste de composants">
                <template #cell(etat)>
                    <AppTrafficLight/>
                </template>
                <template #btn>
                    <AppBtn
                        variant="success"
                        data-bs-toggle="modal"
                        :data-bs-target="target">
                        Créer
                    </AppBtn>
                </template>
            </AppTablePage>
        </div>
    </div>
</template>
