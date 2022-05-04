<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, ref} from 'vue'
    import AppCollectionTableItem from './AppCollectionTableItem.vue'
    import AppCollectionTableUpdateItem from './AppCollectionTableUpdateItem.vue'
    defineProps<{item: ItemField, fields: FormField[]}>()

    const ajout = ref(false)
    const emit = defineEmits<(e: 'update', item: ItemField) => void>()


    function ajoute(): void {
        ajout.value = true
    }
    function AnnuleAjout(): void {
        ajout.value = false
    }
    function update(item: ItemField): void {
        emit('update', item)
    }
</script>

<template>
    <AppCollectionTableItem v-if="!ajout" :fields="fields" :item="item" @ajoute="ajoute" @update="update"/>
    <AppCollectionTableUpdateItem v-else :fields="fields" @annule-ajout="AnnuleAjout"/>
</template>
