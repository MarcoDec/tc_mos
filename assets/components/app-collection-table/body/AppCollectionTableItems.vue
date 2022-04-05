<script setup>
    import {inject, ref} from 'vue'
    import AppCollectionTableItem from './AppCollectionTableItem.vue'

    const emit = defineEmits(['show', 'update'])
    defineProps({
        items: {required: true, type: Array},
        violations: {default: () => [], type: Array}
    })
    const searchMode = inject('searchMode', ref(true))
    const updated = ref(-1)

    function toggle(index) {
        updated.value = updated.value === index ? -1 : index
        if (updated.value !== -1)
            searchMode.value = true
    }

    function show(item) {
        emit('show', item)
    }

    function update(item) {
        emit('update', item)
    }
</script>

<template>
    <tbody>
        <AppCollectionTableItem
            v-for="(item, index) in items"
            :key="item.id"
            :index="index"
            :item="item"
            :updated="updated"
            :violations="violations"
            @show="show"
            @toggle="toggle"
            @update="update"/>
    </tbody>
</template>
