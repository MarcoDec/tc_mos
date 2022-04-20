<script setup>
    import {inject, ref} from 'vue'
    import AppCollectionTableCreate from './AppCollectionTableCreate.vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'
    import AppCollectionTableSearch from './AppCollectionTableSearch.vue'

    defineProps({coll: {required: true, type: Object}, violations: {default: () => [], type: Array}})

    const emit = defineEmits(['create', 'search'])
    const searchMode = inject('searchMode', ref(true))

    function create(createOptions) {
        emit('create', createOptions)
    }

    function search() {
        emit('search')
    }

    function toggle() {
        searchMode.value = !searchMode.value
    }
</script>

<template>
    <thead class="table-dark">
        <AppCollectionTableFields :coll="coll" @click="search"/>
        <AppCollectionTableSearch v-if="searchMode" :coll="coll" @search="search" @toggle="toggle"/>
        <AppCollectionTableCreate v-else :violations="violations" @create="create" @toggle="toggle"/>
    </thead>
</template>
