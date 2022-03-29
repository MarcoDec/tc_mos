<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/employees'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppEmployeeCreate from './AppEmployeeCreate.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un employé'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchCEmployee = useNamespacedActions<Actions>('employees', ['fetchEmployee']).fetchEmployee
    const {items} = useNamespacedGetters<Getters>('employees', ['items'])

    onMounted(async () => {
        await fetchCEmployee()
    })

    const fields = [
        {
            create: true,
            filter: true,
            label: 'Matricule',
            name: 'matricule',
            sort: true,
            type: 'text',
            update: false
        },
        {

            create: true,
            filter: true,
            label: 'Nom',
            name: 'nom',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Prenom',
            name: 'prenom',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'iInitiales',
            name: 'initiales',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Identifiant',
            name: 'identifiant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Services',
            name: 'service',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Compte utilisateur',
            name: 'compte',
            sort: true,
            type: 'boolean',
            update: true
        },

        {
            create: true,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="user-tag"/>
            Employés
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppEmployeeCreate/>
    </AppModal>

    <AppCollectionTable
        :id="route.name"
        :fields="fields"
        :items="items"
        pagination>
        <template #etat="{item}">
            <td><AppTrafficLight :item="item"/></td>
        </template>
    </AppCollectionTable>
</template>
