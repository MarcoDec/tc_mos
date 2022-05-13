<script setup>
    import {computed} from 'vue'
    import useFamiliesStore from '../../stores/purchase/component/family/families'

    const families = useFamiliesStore()
    const fields = computed(() => [
        {label: 'Parent', name: 'parent', options: families.options, type: 'select'},
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'},
        {label: 'Cuivre', name: 'copperable'},
        {label: 'Code douanier', name: 'customsCode'},
        {label: 'Icône', name: 'file', type: 'file'}
    ])
    const props = defineProps({id: {required: true, type: String}})
    const form = computed(() => `${props.id}-create`)
</script>

<template>
    <AppCard :id="id" title="Ajouter une famille">
        <AppForm :id="form" :fields="fields"/>
    </AppCard>
</template>
