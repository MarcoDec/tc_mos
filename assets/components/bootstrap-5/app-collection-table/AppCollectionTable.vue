<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {defineProps, ref} from 'vue'
    import AppCollectionTableAddRow from './AppCollectionTableAddRow.vue'
    import AppCollectionTableBodyHeader from './AppCollectionTableBodyHeader.vue'
    import AppCollectionTableBodyItem from './AppCollectionTableBodyItem.vue'
    import AppCollectionTableHeader from './AppCollectionTableHeader.vue'
    defineProps<{fields: FormField, addRaw: FormField, items: ItemField}>()
    const opened = ref(false)

    function ajout(): void {
        opened.value = true
        console.log('opened', opened)

    }
    function bascule(): void{
        opened.value = false
        console.log('openedd', opened)
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeader :fields="fields"/>
        <tbody>
            <AppCollectionTableBodyHeader v-if="!opened" :fields="fields" @open="ajout"/>
            <AppCollectionTableAddRow v-else :add-raw="addRaw" @close="bascule"/>
            <tr class="bg-dark">
                <td colspan="10"/>
            </tr>
            <AppCollectionTableBodyItem :items="items" :add-raw="addRaw"/>
        </tbody>
    </table>
</template>
