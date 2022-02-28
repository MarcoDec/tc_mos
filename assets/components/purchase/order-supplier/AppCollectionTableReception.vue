<script lang="ts" setup>
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'
    import {Items} from "../../../store/supplierItems/supplierItem/getters";

    const props = defineProps<{items:Items}>()

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

