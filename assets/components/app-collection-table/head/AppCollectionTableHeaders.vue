<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTableCreate from './AppCollectionTableCreate.vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'
    import AppCollectionTableSearch from './AppCollectionTableSearch.vue'

    defineProps({violations: {default: () => [], type: Array}})

    const emit = defineEmits(['create', 'search'])
    const search = ref(true)
    const row = computed(() => (search.value ? AppCollectionTableSearch : AppCollectionTableCreate))

    function create(createOptions) {
        emit('create', createOptions)
    }

    function searchHandler(searchOptions) {
        emit('search', searchOptions)
    }

    function toggle() {
        search.value = !search.value
    }
</script>

<template>
    <thead class="table-dark">
        <AppCollectionTableFields/>
        <component :is="row" :violations="violations" @create="create" @search="searchHandler" @toggle="toggle"/>
    </thead>
</template>
