<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppCardableTableBody from './AppCardableTableBody.vue'

    const props = defineProps({
        currentPage: {required: true, type: String},
        fields: {required: true, type: Array},
        items: {required: true, type: Array},
        pagine: {required: true, type: Boolean}
    })
    const emit = defineEmits(['deleted', 'update'])
    function update(item) {
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }

    function calculIndice(index, currentPage){
        if (props.pagine === false){
            return index + 1 + 15 * currentPage
        }
        return index + 1 + 15 * (currentPage - 1)
    }
</script>

<template>
    <tr v-for="(item, index) in items" :key="index">
        <th scope="row" :title="currentPage">
            {{ calculIndice(index, currentPage) }}
        </th>
        <AppCardableTableBody :item="item" :fields="fields" :indice="index" @update="update" @deleted="deleted"/>
    </tr>
</template>
