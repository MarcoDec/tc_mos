<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppCollectionTableAddRow from './AppCollectionTableAddRow.vue'
    import AppCollectionTableBodyHeader from './AppCollectionTableBodyHeader.vue'
    import AppCollectionTableBodyItem from './AppCollectionTableBodyItem.vue'
    import AppCollectionTableHeader from './AppCollectionTableHeader.vue'
    const prop = defineProps<{fields: FormField[], items: ItemField[], pag: boolean, user: string, min: boolean}>()
    const displayedFileds = computed(() => (prop.min ? prop.fields.filter(({min}) => min) : prop.fields))
    const emit = defineEmits<(e: 'update', item: ItemField) => void>()
    function update(item: ItemField): void {
        emit('update', item)
    }

    const opened = ref(false)

    function ajout(): void {
        opened.value = true
    }
    function bascule(): void{
        opened.value = false
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeader :fields="displayedFileds"/>
        <tbody>
            <AppCollectionTableBodyHeader v-if="!opened" :fields="displayedFileds" :user="user" @open="ajout"/>
            <AppCollectionTableAddRow v-else :fields="displayedFileds" @close="bascule"/>
            <tr class="bg-dark">
                <td colspan="10"/>
            </tr>
            <AppCollectionTableBodyItem :items="items" :fields="displayedFileds" @update="update"/>
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
