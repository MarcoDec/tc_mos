<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppCardableTableBody from './AppCardableTableBody.vue'

    defineProps({
        fields: {required: true, type: Array},
        items: {required: true, type: Array},
        currentPage: {required: true, type: String}
    })
    const emit = defineEmits(['deleted', 'update'])
    function update(item) {
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
</script>

<template>
    <tr v-for="(item, index) in items" :key="index">
        <th scope="row">
            {{ (index + 1 ) + 15 * (currentPage - 1) }}
        </th>
        <AppCardableTableBody :item="item" :fields="fields" @update="update" @deleted="deleted"/>
    </tr>
</template>
