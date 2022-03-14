<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/suppliers'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppSupplierCreate from './AppSupplierCreate.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un Fournisseur'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchSuppliers = useNamespacedActions<Actions>('suppliers', ['fetchSuppliers']).fetchSuppliers
    const {items} = useNamespacedGetters<Getters>('suppliers', ['items'])

    onMounted(async () => {
        await fetchSuppliers()
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
            <Fa class="me-3" icon="user-tag"/>
            Fournisseurs
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppSupplierCreate/>
    </AppModal>

    <AppCollectionTable :id="route.name" :fields="fields" :items="items" pagination>
        <template #etat="{item}">
            <td><AppTrafficLight :item="item"/></td>
        </template>
    </AppCollectionTable>
</template>
