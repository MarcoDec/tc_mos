<script setup>
    import AppTableSimpleField from './AppTableSimpleField.vue'
    import AppTableSortableField from './AppTableSortableField.vue'

    defineProps({field: {required: true, type: Object}, store: {required: true, type: Object}})
</script>

<template>
    <AppTableSortableField v-if="field.name" :field="field" :store="store"/>
    <AppTableSimpleField v-else :field="field"/>
</template>
