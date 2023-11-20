<script setup>
    import {computed, onUnmounted, toRefs} from 'vue'
    import AppTreeFormCreate from './AppTreeFormCreate.vue'
    import AppTreeFormUpdate from './AppTreeFormUpdate.vue'
    import useFields from '../../../../stores/field/fields'
    import {useRoute} from 'vue-router'

    const {fields, machine, tree} = toRefs(defineProps({
        fields: {required: true, type: Array},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    }))
    const route = useRoute()
    const storedFields = useFields(route.name, fields.value)
    await storedFields.fetch()
    const form = computed(() => (tree.value.hasSelected ? AppTreeFormUpdate : AppTreeFormCreate))

    onUnmounted(() => {
        storedFields.dispose()
    })
</script>

<template>
    <component :is="form" :key="tree.selectedKey" :fields="storedFields" :machine="machine" :tree="tree"/>
</template>
