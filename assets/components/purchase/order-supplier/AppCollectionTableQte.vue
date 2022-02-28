<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/supplierItems'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'
    import {defineProps, onMounted} from 'vue'
    import {useRoute} from 'vue-router'
    import {Items} from "../../../store/supplierItems/supplierItem/getters";


    const props = defineProps<{items:Items}>()

    const route = useRoute()
    const fields
        = [
            {
                label: 'Composant'
            },
            {
                label: 'Date Récéption'
            },
            {
                label: 'Status'
            },
            {
                label: 'Commentaire'
            },
            {
                label: 'Controle'
            },
            {
                label: 'Valeur'
            }
        ]


</script>

<template>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

