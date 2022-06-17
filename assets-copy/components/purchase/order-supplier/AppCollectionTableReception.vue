<script lang="ts" setup>
    import type {Items} from '../../../store/supplierItems/supplierItem/getters'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{items: Items}>()

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
</script>

<template>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

