<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppCollectionTableAddRow from './AppCollectionTableAddRow.vue'
    import AppCollectionTableBodyHeader from './AppCollectionTableBodyHeader.vue'
    import AppCollectionTableBodyItem from './AppCollectionTableBodyItem.vue'
    import AppCollectionTableHeader from './AppCollectionTableHeader.vue'
    const props = defineProps({
        create: {default: false, type: Boolean},
        fields: {required: true, type: Array},
        form: {required: false, type: String},
        items: {required: true, type: Array},
        min: {default: false, type: Boolean},
        pag: {default: false, type: Boolean},
        user: {required: false, type: String}
    })
    const displayedFileds = computed(() => (props.min ? props.fields.filter(({min}) => min) : props.fields))
    const emit = defineEmits(['update'])
    function update(item){
        emit('update', item)
    }

    const opened = ref(false)

    function ajout() {
        opened.value = true
    }
    function bascule(){
        opened.value = false
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeader :fields="displayedFileds"/>
        <tbody>
            <AppCollectionTableBodyHeader v-if="!opened" :form="form" :create="create" :fields="displayedFileds" :user="user" @open="ajout"/>
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
