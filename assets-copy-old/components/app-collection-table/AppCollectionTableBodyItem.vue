<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppCollectionTableBody from './AppCollectionTableBody.vue'

    defineProps({
        fields: {required: true, type: Array},
        items: {required: true, type: Array}
    })
    const emit = defineEmits(['update'])
    function update(item) {
        emit('update', item)
    }
</script>

<template>
    <tr v-for="(item, index) in items" :key="index">
        <th scope="row">
            {{ index + 1 }}
        </th>
        <AppCollectionTableBody :item="item" :fields="fields" @update="update"/>
    </tr>
</template>
