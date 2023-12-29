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
        title: {default: null, required: false, type: String}
    })
    //console.log('props.fields', props.fields)
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
        <AppCardableTableHeader :fields="displayedFields" :title="title" @trier-alphabet="trierAlphabet">
            <template #title>
                <slot name="title"/>
            </template>
        </AppCardableTableHeader>
        <tbody>
            <AppCardableTableBodyHeader :form="form" :fields="displayedFields" :user="user" :model-value="input" @search="search" @cancel-search="cancelSearch" @update:model-value="onUpdateSearchModelValue"/>
            <tr class="bg-dark">
                <td colspan="20"/>
            </tr>
            <AppCardableTableBodyItem :items="items" :fields="displayedFields" :current-page="currentPage" :pagine="pag" :should-delete="shouldDelete" :should-see="shouldSee" @update="update" @deleted="deleted"/>
        </tbody>
    </table>
    <nav v-if="pag" aria-label="Page navigation example">
        <ul class="pagination">
            <li v-if="firstPage && firstPage < currentPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(firstPage)">Début</a>
            </li>
            <li v-if="previousPage && previousPage < currentPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(previousPage)">Préc.</a>
            </li>
            <li class="page-item">
                <span class="bg-light page-link text-black">{{ currentPage }}</span>
            </li>
            <li v-if="nextPage && nextPage > currentPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(nextPage)">Suiv.</a>
            </li>
            <li v-if="lastPage && lastPage > currentPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="getPage(lastPage)">Fin</a>
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
