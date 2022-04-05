<script setup>
    import {computed, provide, ref} from 'vue'
    import AppCollectionTableHeaders from './head/AppCollectionTableHeaders.vue'
    import AppCollectionTableItems from './body/AppCollectionTableItems.vue'
    import AppPagination from '../bootstrap-5/pagination/AppPagination.vue'

    const emit = defineEmits(['create', 'search', 'show', 'update'])
    const props = defineProps({
        create: {required: false, type: Boolean},
        currentPage: {default: 1, type: Number},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        pagination: {required: false, type: Boolean},
        violations: {default: () => [], type: Array}
    })
    const searchMode = ref(true)
    const count = computed(() => props.items.length)

    function createHandler(createOptions) {
        emit('create', createOptions)
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
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders :violations="violations" @create="createHandler" @search="search"/>
        <AppCollectionTableItems :items="items" :violations="violations" @show="show" @update="update"/>
    </table>
    <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>
</template>
