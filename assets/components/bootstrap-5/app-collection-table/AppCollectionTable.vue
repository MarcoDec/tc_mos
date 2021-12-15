<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {defineProps, ref} from 'vue'
    import AppCollectionTableAddRow from './AppCollectionTableAddRow.vue'
    import AppCollectionTableBodyHeader from './AppCollectionTableBodyHeader.vue'
    import AppCollectionTableBodyItem from './AppCollectionTableBodyItem.vue'
    import AppCollectionTableHeader from './AppCollectionTableHeader.vue'
    const prop = defineProps<{fields: FormField, items: ItemField, pag: boolean, user: string}>()
    console.log('propaaaaaa', prop)


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
            <AppCollectionTableBodyHeader v-if="!opened" :fields="fields" :user="user" @open="ajout"/>
            <AppCollectionTableAddRow v-else :fields="fields" @close="bascule"/>
            <tr class="bg-dark">
                <td colspan="10"/>
            </tr>
            <AppCollectionTableBodyItem :items="items" :fields="fields"/>
        </tbody>
    </table>
    <nav v-if="pag" aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="#">Début</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Préc.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Suiv.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Fin</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.pagination{
    float: right;
}
</style>
