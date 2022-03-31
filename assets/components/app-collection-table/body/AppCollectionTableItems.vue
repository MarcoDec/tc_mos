<script setup>
    const emit = defineEmits(['update'])
    defineProps({items: {required: true, type: Array}})

    function update(item) {
        emit('update', item)
    }
</script>

<template>
    <tbody>
        <AppCollectionTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item" @update="update"/>
    </tbody>
</template>
