<script setup>
    import {computed, inject, ref} from 'vue'
    import AppCollectionTableCreate from './AppCollectionTableCreate.vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'
    import AppCollectionTableSearch from './AppCollectionTableSearch.vue'

    defineProps({violations: {default: () => [], type: Array}})

    const emit = defineEmits(['create', 'search'])
    const searchMode = inject('searchMode', ref(true))
    const row = computed(() => (searchMode.value ? AppCollectionTableSearch : AppCollectionTableCreate))

    function create(createOptions) {
        emit('create', createOptions)
    }

    function searchHandler(searchOptions) {
        emit('search', searchOptions)
    }

    function toggle() {
        searchMode.value = !searchMode.value
    }
</script>

<template>
    <thead class="table-dark">
        <AppCollectionTableFields/>
        <component :is="row" :violations="violations" @create="create" @search="searchHandler" @toggle="toggle"/>
    </thead>
</template>
