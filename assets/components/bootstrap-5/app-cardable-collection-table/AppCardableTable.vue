<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppCardableTableBodyHeader from './body/AppCardableTableBodyHeader.vue'
    import AppCardableTableBodyItem from './body/AppCardableTableBodyItem.vue'
    import AppCardableTableHeader from './head/AppCardableTableHeader.vue'

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
    let input =ref('')
    const emit = defineEmits(['deleted', 'getPage', 'update','trierAlphabet','update:modelValue', 'search', 'cancelSearch'])
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
        // console.log("AppInputGuesser:", inputValues)
        emit('search', inputValues)
    }
    async function cancelSearch(inputValues) {
        input.value = inputValues
        emit('cancelSearch',inputValues)
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCardableTableHeader :fields="displayedFileds" @trierAlphabet="trierAlphabet"/>
        <tbody>
            <AppCardableTableBodyHeader :form="form" :fields="displayedFileds" :user="user" @search="search" @cancelSearch="cancelSearch" :model-value="input" />
            <tr class="bg-dark">
                <td colspan="10"/>
            </tr>
            <AppCardableTableBodyItem :items="items" :fields="displayedFileds" :current-page="currentPage" @update="update" @deleted="deleted"/>
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
