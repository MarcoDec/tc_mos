<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'

    const search = ref(true)
    const row = computed(() => (search.value ? 'AppCollectionTableSearch' : 'AppCollectionTableAdd'))

    function toggle() {
        search.value = !search.value
    }
</script>

<template>
    <thead class="table-dark">
        <AppCollectionTableFields/>
        <component :is="row" @toggle="toggle"/>
    </thead>
</template>
