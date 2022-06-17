<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/engines'
    import {computed, onMounted} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import AppEngineCreate from './AppEngineCreate.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchEngine = useNamespacedActions<Actions>('engines', [
        'fetchEngine'
    ]).fetchEngine
    const {items} = useNamespacedGetters<Getters>('engines', ['items'])

    onMounted(async () => {
        await fetchEngine()
    })

    const fields = [
        {
            create: true,
            filter: true,
            label: 'Img',
            name: 'img',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
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
            label: 'Groupe',
            name: 'groupe',
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
        },
        {
            create: true,
            filter: true,
            label: 'Période',
            name: 'periode',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="tools"/>
            Equipements
        </h1>
        <AppCol>
            <AppBtn variant="success" data-bs-toggle="modal" :data-bs-target="target">
                Créer
            </AppBtn>
        </AppCol>
    </AppRow>

    <AppModal :id="modalId" class="four" :title="title">
        <AppEngineCreate/>
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
