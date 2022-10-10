<script setup>
    import AppTableItem from './AppTableItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
</script>

<template>
    <tbody>
        <AppTableItem
            v-for="(row, i) in store.rows"
            :key="row.id"
            :fields="fields"
            :index="i"
            :item="row"
            :send="send"/>
    </tbody>
</template>
