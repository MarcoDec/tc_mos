<script setup>
    import {computed, onUnmounted} from 'vue'
    import AppTreeFormCreate from './AppTreeFormCreate.vue'
    import AppTreeFormUpdate from './AppTreeFormUpdate.vue'
    import useFields from '../../../stores/field/fields'
    import {useRoute} from 'vue-router'

    const props = defineProps({machine: {required: true, type: Object}, tree: {required: true, type: Object}})
    const route = useRoute()
    const fields = useFields(route.name, [
        {label: 'Nom', name: 'name'},
        {label: 'Code douanier', name: 'customsCode'},
        {label: 'Icône', name: 'file', type: 'file'}
    ])
    const form = computed(() => (props.tree.hasSelected ? AppTreeFormUpdate : AppTreeFormCreate))

    onUnmounted(() => {
        fields.dispose()
    })
</script>

<template>
    <component :is="form" :key="tree.selectedKey" :fields="fields" :machine="machine" :tree="tree"/>
</template>
