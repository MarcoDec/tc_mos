<script setup>
    import {defineEmits, defineProps, ref} from 'vue'
    import AppCardableTableItem from './AppCardableTableItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
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
    <AppCardableTableItem  :fields="fields" :item="item"  @update="update" @deleted="deleted" /> 
</template>
