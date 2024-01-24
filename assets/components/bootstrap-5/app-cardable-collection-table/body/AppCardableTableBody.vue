<script setup>
    import AppCardableTableItem from './AppCardableTableItem.vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        shouldDelete: {required: false, default: true},
        shouldSee: {required: false, default: true}
    })
    console.log('AppCardableTableBody', props.fields, props.item)
    const emit = defineEmits(['deleted', 'update'])
    function update(item){
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
</script>

<template>
    <AppCardableTableItem :fields="fields" :item="item" :should-delete="shouldDelete" :should-see="shouldSee" @update="update" @deleted="deleted"/>
</template>
