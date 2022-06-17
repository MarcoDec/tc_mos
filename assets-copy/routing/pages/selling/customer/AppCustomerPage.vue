<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/customers'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppCustomerCreate from './AppCustomerCreate.vue'

    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un client'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchCustomers = useNamespacedActions<Actions>('customers', ['fetchCustomers']).fetchCustomers
    const {items} = useNamespacedGetters<Getters>('customers', ['items'])

    onMounted(async () => {
        await fetchCustomers()
    })

    const fields = [
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
            <Fa class="me-3" icon="user-tie"/>
            Clients
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppCustomerCreate/>
    </AppModal>

    <AppCollectionTable :id="route.name" :fields="fields" :items="items" pagination>
        <template #etat="{item}">
            <td><AppTrafficLight :item="item"/></td>
        </template>
    </AppCollectionTable>
</template>
