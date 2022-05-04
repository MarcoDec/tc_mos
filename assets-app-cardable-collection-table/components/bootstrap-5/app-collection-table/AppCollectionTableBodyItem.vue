<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps} from 'vue'
    import AppCollectionTableBody from './AppCollectionTableBody.vue'

    defineProps<{items: ItemField[], fields: FormField[]}>()
    const emit = defineEmits<(e: 'update', item: ItemField) => void>()
    function update(item: ItemField): void {
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

