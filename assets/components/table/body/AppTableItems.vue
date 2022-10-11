<script setup>
    import AppTableItem from './AppTableItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
</script>

<template>
    <tbody :id="id">
        <AppTableItem
            v-for="(row, i) in store.rows"
            :key="row.id"
            :body="id"
            :fields="fields"
            :index="i"
            :item="row"
            :machine="machine"/>
    </tbody>
</template>
