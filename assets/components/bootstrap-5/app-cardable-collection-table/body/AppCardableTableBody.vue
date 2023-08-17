<script setup>
    import AppCardableTableItem from './AppCardableTableItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        indice: {required: true, type: Number},
        shouldDelete: {required: false, default: true}
    })

    const emit = defineEmits(['deleted', 'update'])
    function update(item){
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
</script>

<template>
    <AppCardableTableItem :fields="fields" :item="item" :indice="indice" :should-delete="shouldDelete" @update="update" @deleted="deleted"/>
</template>
