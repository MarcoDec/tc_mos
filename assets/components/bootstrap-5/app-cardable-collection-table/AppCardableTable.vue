<script setup>
    import {computed, ref} from 'vue'
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
        pag: {required: true, type: Boolean},
        previousPage: {required: true, type: String},
        user: {required: true, type: String},
        shouldDelete: {required: false, default: true},
        shouldSee: {required: false, default: true},
        title: {default: null, required: false, type: String},
        topOffset: {default: '0px', type: String}
    })
    //Conversion des champs String en entier
    const pageCurrent = computed(() => parseInt(props.currentPage))
    const pageFirst = computed(() => parseInt(props.firstPage))
    const pageLast = computed(() => parseInt(props.lastPage))
    const pageNext = computed(() => parseInt(props.nextPage))
    const pagePrevious = computed(() => parseInt(props.previousPage))
    const displayedFields = computed(() => (props.min ? props.fields.filter(({min}) => min) : props.fields))
    const input = ref({})
    const emit = defineEmits(['deleted', 'getPage', 'update', 'trierAlphabet', 'update:modelValue', 'update:searchModelValue', 'search', 'cancelSearch'])
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
    function onUpdateSearchModelValue(data) {
        input.value[data.field] = data.event
        emit('update:searchModelValue', input.value)
    }
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCardableTableHeader :fields="displayedFields" :title="title" :top-offset="topOffset" @trier-alphabet="trierAlphabet">
            <template #title>
                <slot name="title"/>
            </template>
            <template #form>
                <AppCardableTableBodyHeader :form="form" :fields="displayedFields" :user="user" :model-value="input" @search="search" @cancel-search="cancelSearch" @update:model-value="onUpdateSearchModelValue"/>
            </template>
        </AppCardableTableHeader>
        <tbody>
            <AppCardableTableBodyItem :items="items" :fields="displayedFields" :current-page="currentPage" :pagine="pag" :should-delete="shouldDelete" :should-see="shouldSee" @update="update" @deleted="deleted"/>
        </tbody>
    </table>
    <nav v-if="pag" aria-label="Page navigation example">
        <ul class="pagination">
            <li v-if="pageFirst && pageFirst < pageCurrent" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(pageFirst)">1</a>
            </li>
            <li v-if="pagePrevious && pagePrevious < pageCurrent && pagePrevious > 1" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(pagePrevious)">... {{ pagePrevious }}</a>
            </li>
            <li class="page-item">
                <span v-if="pageCurrent" class="bg-light page-link text-black"><b>Page {{ pageCurrent }}</b></span>
                <span v-else class="bg-light page-link text-black"><b>Aucun élément trouvé</b></span>
            </li>
            <li v-if="pageNext && pageNext > pageCurrent && pageNext < pageLast" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(pageNext)">{{ pageNext }} ...</a>
            </li>
            <li v-if="pageLast && pageLast > pageCurrent" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(pageLast)">{{ pageLast }}</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
    .pagination{
        float: right;
        margin-right:20px;
    }
</style>
