<script setup>
    import AppEmployeeCreate from './AppEmployeeCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useEmployees from '../../../stores/employee/employees'
    import {useTableMachine} from '../../../machine'

    const title = 'Créer un Employee'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineEmployee = useTableMachine('machine-employee')
    const employees = useEmployees()

    const fields = computed(() => [
        {
            create: true,
            label: 'Matricule',
            name: 'matricule',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            label: 'Nom',
            name: 'nom',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Prenom',
            name: 'prenom',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'iInitiales',
            name: 'initiales',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Identifiant',
            name: 'identifiant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Services',
            name: 'service',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Compte utilisateur',
            name: 'compte',
            sort: true,
            type: 'boolean',
            update: true
        },

        {
            create: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        }
    ])
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title">
            <AppEmployeeCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineEmployee"
                :store="employees"
                title="La liste de Employees">
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
