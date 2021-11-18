<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {defineProps, ref} from 'vue'
    import AppCollectionTableItem from './AppCollectionTableItem.vue'
    import AppCollectionTableUpdateItem from './AppCollectionTableUpdateItem.vue'
    defineProps<{item: ItemField, addRaw: FormField}>()
    const updated = ref(false)

    function update(): void {
        updated.value = true
        console.log('updated', updated)
    }
    function AnnuleUpdate(): void {
        updated.value = false
        console.log('updated', updated)
    }
</script>

<template>
    <AppCollectionTableItem v-if="!updated" :item="item" @update="update"/>
    <AppCollectionTableUpdateItem v-else :add-raw="addRaw" @annule-update="AnnuleUpdate"/>
</template>
