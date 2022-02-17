<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/supplierItems'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import {onMounted} from 'vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const fields = [
        {
            label: 'Composant'
        },
        {
            label: 'Quantité Confirmée'
        },
        {
            label: 'Quantité Reçue'
        },
        {
            label: 'Date Récéption'
        },
        {
            label: 'Status'
        },
        {
            label: 'Commentaire'
        }

    ]

    const fetchItem = useNamespacedActions<Actions>('supplierItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('supplierItems', ['items'])

    onMounted(async () => {
        await fetchItem()
    })
</script>

<template>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

