<script setup>
    import AppTableItem from './AppTableItem.vue'

    defineProps({
        action: {type: Boolean},
        disableRemove: {type: Boolean},
        enableShow: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const emits = defineEmits(['show'])
    function show(data) {
        emits('show', data)
    }
</script>

<template>
    <tbody :id="id">
        <AppTableItem
            v-for="(row, i) in store.rows"
            :key="row.id"
            :action="action"
            :body="id"
            :disable-remove="disableRemove"
            :enable-show="enableShow"
            :fields="fields"
            :index="i"
            :item="row"
            :machine="machine"
            @show="show"/>
    </tbody>
</template>
