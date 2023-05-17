<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppCollectionTableBodyHeader from './body/AppCollectionTableBodyHeader.vue'
    import AppCollectionTableBodyItem from './body/AppCollectionTableBodyItem.vue'
    import AppCollectionTableHeader from './head/AppCollectionTableHeader.vue'

    const props = defineProps({
        currentPage: {required: true, type: String},
        fields: {required: true, type: Array},
        firstPage: {required: true, type: String},
        form: {required: true, type: String},
        items: {required: true, type: Array},
        lastPage: {required: true, type: String},
        min: {type: Boolean},
        nextPage: {required: true, type: String},
        pag: {type: Boolean},
        previousPage: {required: true, type: String},
        user: {required: true, type: String}
    })
    const displayedFileds = computed(() => (props.min ? props.fields.filter(({min}) => min) : props.fields))
    const input = ref({})
    console.log('je suis ici', input)
    const emit = defineEmits(['deleted', 'getPage', 'update', 'trierAlphabet', 'update:modelValue', 'search', 'cancelSearch', 'ajout'])
    function update(item){
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
    function getPage(nPage){
        emit('getPage', nPage)
    }
    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
    function search(inputValues) {
        emit('search', inputValues)
    }
    async function cancelSearch(inputValues) {
        input.value = inputValues
        emit('cancelSearch', inputValues)
    }
    const opened = ref(false)

    function ajouter(inputValues) {
        opened.value = false
        input.value = inputValues
        emit('ajout', inputValues)
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeader :fields="displayedFileds" @trier-alphabet="trierAlphabet"/>
        <tbody>
            <AppCollectionTableBodyHeader v-if="!opened" :form="form" :fields="displayedFileds" :user="user" :model-value="input" @search="search" @cancel-search="cancelSearch" @ajout="ajouter"/>
            <!-- <AppCollectionTableAddRow v-else :fields="displayedFileds" @close="bascule" @ajout="ajouter" :model-value="input"/> -->
            <tr class="bg-dark">
                <td colspan="11"/>
            </tr>
            <AppCollectionTableBodyItem :items="items" :fields="displayedFileds" :current-page="currentPage" @update="update" @deleted="deleted"/>
        </tbody>
    </table>
    <nav v-if="pag" aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(firstPage)">Début</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(previousPage)">Préc.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(currentPage)">{{ currentPage }}</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(nextPage)">Suiv.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(lastPage)">Fin</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
    .pagination{
        float: right;
    }
</style>
