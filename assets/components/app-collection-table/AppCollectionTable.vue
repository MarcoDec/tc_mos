<script setup>
    import {provide, ref} from 'vue'
    import AppCollectionTableHeaders from './head/AppCollectionTableHeaders.vue'
    import AppCollectionTableItems from './body/AppCollectionTableItems.vue'
    import AppPagination from '../bootstrap-5/pagination/AppPagination.vue'

    const emit = defineEmits(['create', 'delete', 'page', 'search', 'show', 'update'])
    const props = defineProps({
        coll: {required: true, type: Object},
        create: {required: false, type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        pagination: {required: false, type: Boolean},
        stateMachine: {required: true, type: String},
        violations: {default: () => [], type: Array}
    })
    const searchMode = ref(true)

    function createHandler(createOptions) {
        emit('create', createOptions)
    }

    async function deleteHandler(id) {
        emit('delete', id)
    }

    function pageHandler(page) {
        emit('page', page)
    }

    function search(searchOptions) {
        emit('search', searchOptions)
    }

    function show(item) {
        emit('show', item)
    }

    function update(item) {
        emit('update', item)
    }

    provide('create', props.create)
    provide('fields', props.fields)
    provide('searchMode', searchMode)
    provide('stateMachine', props.stateMachine)
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders :violations="violations" @create="createHandler" @search="search"/>
        <AppCollectionTableItems
            :items="items"
            :violations="violations"
            @delete="deleteHandler"
            @show="show"
            @update="update"/>
    </table>
    <AppPagination v-if="pagination" :coll="coll" class="float-end" @click="pageHandler"/>
</template>
