<script setup>
    import AppTableSimpleField from './AppTableSimpleField.vue'
    import AppTableSortableField from './AppTableSortableField.vue'

    defineProps({
        field: {required: true, type: Object},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
</script>

<template>
    <AppTableSortableField v-if="field.sort" :field="field" :send="send" :store="store"/>
    <AppTableSimpleField v-else :field="field"/>
</template>
