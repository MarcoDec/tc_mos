<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTableAdd from './AppCollectionTableAdd.vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'
    import AppCollectionTableSearch from './AppCollectionTableSearch.vue'

    const emit = defineEmits(['search'])
    const search = ref(true)
    const row = computed(() => (search.value ? AppCollectionTableSearch : AppCollectionTableAdd))

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
        <component :is="row" @search="searchHandler" @toggle="toggle"/>
    </thead>
</template>
