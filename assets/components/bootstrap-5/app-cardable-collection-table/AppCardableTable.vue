<script setup>
    import {computed, ref} from 'vue'
    import AppCardableTableBodyHeader from './body/AppCardableTableBodyHeader.vue'
    import AppCardableTableBodyItem from './body/AppCardableTableBodyItem.vue'
    import AppCardableTableHeader from './head/AppCardableTableHeader.vue'
    import api from '../../../api'

    const props = defineProps({
        currentPage: {required: true, type: [String, Number]},
        currentFilterAndSortIri: {required: false, default: null, type: String},
        canExportTable: {required: false, default: false, type: Boolean},
        fields: {required: true, type: Array},
        firstPage: {required: true, type: [String, Number]},
        form: {required: true, type: String},
        items: {required: true, type: Array},
        lastPage: {required: true, type: [String, null]},
        min: {type: Boolean},
        nextPage: {required: true, type: [String, null]},
        pag: {required: true, type: Boolean},
        previousPage: {required: true, type: [String, null]},
        user: {required: true, type: String},
        shouldDelete: {required: false, default: true},
        shouldSee: {required: false, default: true},
        title: {default: null, required: false, type: String},
        topOffset: {default: '0px', type: String}
    })
    console.log('title', props.title)
    console.log('currentFilterAndSort', props.currentFilterAndSortIri)
    console.log('canExportTable', props.canExportTable)
    const currentTitle = computed(() => {
        if (props.title === null && props.canExportTable) {
            return ''
        }
        return props.title
    })
    //Conversion des champs String en entier
    const pageCurrent = computed(() => parseInt(props.currentPage))
    const pageFirst = computed(() => parseInt(props.firstPage))
    const pageLast = computed(() => parseInt(props.lastPage))
    const pageNext = computed(() => parseInt(props.nextPage))
    const pagePrevious = computed(() => parseInt(props.previousPage))
    const displayedFields = computed(() => (props.min ? props.fields.filter(({min}) => min) : props.fields))
    const input = ref({})
    const emit = defineEmits([
        'deleted',
        'getPage',
        'update',
        'trierAlphabet',
        'update:modelValue',
        'update:searchModelValue',
        'search',
        'cancelSearch'
    ])
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
    async function cancelSearch() {
        input.value = {}
        emit('cancelSearch')
    }
    function onUpdateSearchModelValue(data) {
        input.value[data.field] = data.event
        emit('update:searchModelValue', input.value)
    }
    function exportTable() {
        console.log('export table')
        // On récupères les données du back
        api(props.currentFilterAndSortIri, 'GET').then(
            response => {
                const items = response['hydra:member']
                const mappedItems = items.map(item => {
                    const mappedItem = {}
                    console.log('item', item)
                    displayedFields.value.forEach(field => {
                        console.log('field', field)
                        if (typeof field.sourceName !== 'undefined') {
                            // If the field has a sourceName, use it to map the item
                            // If sourceName is composed of dots, we need to navigate through the item object
                            const sourceNames = field.sourceName.split('.')
                            let source = item
                            sourceNames.forEach(sourceName => {
                                source = source[sourceName]
                            })
                            mappedItem[field.name] = source
                            return
                        }
                        if (field.type === 'select') {
                            // On récupère la valeur de l'option
                            const option = field.options.options.find(option2 => option2.value === item[field.name])
                            mappedItem[field.name] = option ? option.text : null
                            return
                        }
                        mappedItem[field.name] = item[field.name]
                    })
                    return mappedItem
                })
                // Transform items into CSV format
                const csvContent = [
                    displayedFields.value.map(field => field.label).join(','), // Header row
                    ...mappedItems.map(item => displayedFields.value.map(field => item[field.name]).join(','))
                ].join('\n')

                // Create a Blob with the CSV
                const blob = new Blob([csvContent], {type: 'text/csv;charset=utf-8;'})
                const link = document.createElement('a')
                const url = URL.createObjectURL(blob)
                link.setAttribute('href', url)
                link.setAttribute('download', 'exported_data.csv')
                link.style.visibility = 'hidden'
                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)
            }
        )
    }
</script>

<template>
    <div class="table-container">
        <table class="table table-bordered table-hover table-striped">
            <AppCardableTableHeader :fields="displayedFields" :title="currentTitle" :top-offset="topOffset" @trier-alphabet="trierAlphabet">
                <template #title>
                    <button v-if="canExportTable" class="btn-export" @click="exportTable">
                        Export CSV
                    </button>
                    <slot name="title"/>
                </template>
                <template #form>
                    <AppCardableTableBodyHeader :form="form" :fields="displayedFields" :user="user" :model-value="input" @search="search" @cancel-search="cancelSearch" @update:model-value="onUpdateSearchModelValue"/>
                </template>
            </AppCardableTableHeader>
            <tbody>
                <AppCardableTableBodyItem
                    :items="items"
                    :fields="displayedFields"
                    :should-delete="shouldDelete"
                    :should-see="shouldSee"
                    @update="update"
                    @deleted="deleted"/>
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
    </div>
</template>

<style scoped>
    .pagination{
        float: right;
        margin-right:20px;
    }
    .btn-export {
        float: right;
        top: 0;
        right: 0;
        padding: 6px 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 10px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .btn-export:hover {
        background-color: #0056b3;
    }
</style>
