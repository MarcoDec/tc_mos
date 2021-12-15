<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import AppCollectionTableBody from './AppCollectionTableBody.vue'
    import {defineProps} from 'vue'
    defineProps<{items: ItemField, fields: FormField}>()
</script>

<template>
    <tr v-for="(item, index) in items" :key="index">
        <th scope="row">
            {{ index + 1 }}
        </th>
        <AppCollectionTableBody :item="item" :fields="fields"/>
    </tr>
</template>

