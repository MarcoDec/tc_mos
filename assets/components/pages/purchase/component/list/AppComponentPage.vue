<script setup>
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {useRouter} from 'vue-router'
    import useComponentsStore from '../../../../../stores/component/components'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../stores/option/options'
    import useUser from '../../../../../stores/security'
    import AppComponentCreateModal from './create/AppComponentCreateModal.vue'
    import Fa from '../../../../Fa'
    import {Modal} from 'bootstrap'

    //region déclaration des constantes
    const router = useRouter()
    const fetchUser = useUser()
    const StoreComponents = useComponentsStore()
    const fetchOptionsComponentFamilies = useOptions('component-families')
    const componentListCriteria = useFetchCriteria('component-list-criteria')
    const tableKey = ref(0)
    const createModalRef = ref(null)
    const creationSuccess = ref(false)
    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'draft', value: 'draft'},
        {text: 'to_validate', value: 'to_validate'},
        {text: 'warning', value: 'warning'}
    ]
    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || fetchUser.isPurchaseAdmin
    const roleUser = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')
    //endregion
    //region chargement des données
    //endregion
    //region déclaration des variables calculées
    const optionsComponentFamilies = computed(() =>
        fetchOptionsComponentFamilies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const fields = computed(() => [
        {
            label: 'Img',
            name: 'filePath',
            trie: false,
            type: 'img',
            width: 100,
            filter: false
        },
        {
            label: 'Réf',
            name: 'code',
            trie: true,
            type: 'text',
            width: 80
        },
        {
            label: 'Ind.',
            name: 'index',
            trie: true,
            type: 'text',
            width: 80
        },
        {
            label: 'Désignation',
            name: 'name',
            trie: true,
            type: 'text'
        },
        {
            label: 'Famille',
            name: 'family',
            type: 'select',
            options: {
                label: value =>
                    optionsComponentFamilies.value.find(option => option.value === value)?.text ?? null,
                options: optionsComponentFamilies.value
            }
        },
        // {
        //     label: 'Fournisseurs',
        //     name: 'fournisseurs',
        //     trie: false,
        //     type: 'text'
        // },
        // {
        //     label: 'Stocks',
        //     name: 'stocks',
        //     trie: false,
        //     type: 'text'
        // },
        // {
        //     label: 'Besoins enregistrés',
        //     name: 'besoin',
        //     trie: false,
        //     type: 'text'
        // },
        {
            label: 'Etat',
            name: 'state',
            trie: false,
            options: {
                label: value =>
                    optionsEtat.find(option => option.type === value)?.text ?? null,
                options: optionsEtat
            },
            type: 'select',
            width: 100
        }
    ])
    const itemsTable = computed(() => StoreComponents.componentItems)
    //endregion
    //region déclaration des fonctions
    async function refreshTable() {
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    function onComponentShowRequest(item) {
        /* eslint-disable camelcase */
        router.push({name: 'component', params: {id_component: item.id}})
    }
    async function cancelSearch() {
        componentListCriteria.resetAllFilter()
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function deleted(id){
        await StoreComponents.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        componentListCriteria.gotoPage(parseFloat(nPage))
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function search(inputValues) {
        componentListCriteria.resetAllFilter()
        if (inputValues.code) componentListCriteria.addFilter('code', inputValues.code)
        if (inputValues.index) componentListCriteria.addFilter('index', inputValues.index)
        if (inputValues.name) componentListCriteria.addFilter('name', inputValues.name)
        if (inputValues.family) componentListCriteria.addFilter('family', inputValues.family)
        if (inputValues.state) componentListCriteria.addFilter('embState.state[]', inputValues.state)
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        componentListCriteria.addSort(payload.name, payload.direction)
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function onCreated() {
        await refreshTable()
        if (createModalRef.value) {
            const modalElement = createModalRef.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
            creationSuccess.value = true
            setTimeout(() => {
                creationSuccess.value = false
            }, 3000)
        }
    }
    //endregion

    onMounted(() => {
        // console.log('onMounted', fetchOptionsComponentFamilies)
        fetchOptionsComponentFamilies.fetchOp().then(() => {
            // console.log('Component Families loaded', optionsComponentFamilies.value)
            tableKey.value += 1
        })
        StoreComponents.fetch().then(() => {
            // console.log('Components loaded')
            tableKey.value += 1
        })
    })
    onUnmounted(() => {
        StoreComponents.reset()
        fetchOptionsComponentFamilies.resetItems()
        componentListCriteria.reset()
    })
</script>

<template>
    <div class="row">
        <div class="row">
            <div class="col">
                <h1>
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    Liste des composants
                    <span v-if="isPurchaseWriterOrAdmin" class="btn-float-right">
                        <AppBtn
                            variant="success"
                            label="Créer"
                            data-bs-toggle="modal"
                            data-bs-target="#target">
                            <Fa icon="plus"/>
                            Créer
                        </AppBtn>
                    </span>
                </h1>
            </div>
        </div>
        <div v-if="creationSuccess" class="row d-flex">
            <div class="bg-success text-white text-center">
                Composant bien créé
            </div>
        </div>
        <AppComponentCreateModal
            ref="createModalRef"
            :store-component="StoreComponents" @created="onCreated"/>
        <div class="col">
            <Suspense>
                <AppCardableTable
                    :key="tableKey"
                    :current-page="StoreComponents.currentPage?.toString() ?? ''"
                    :fields="fields"
                    :first-page="StoreComponents.firstPage?.toString() ?? ''"
                    :items="itemsTable"
                    :last-page="StoreComponents.lastPage?.toString() ?? ''"
                    :next-page="StoreComponents.nextPage?.toString() ?? ''"
                    :pag="StoreComponents.pagination"
                    :previous-page="StoreComponents.previousPage?.toString() ?? ''"
                    :user="roleUser"
                    form="formComponentCardableTable"
                    @cancel-search="cancelSearch"
                    @deleted="deleted"
                    @get-page="getPage"
                    @search="search"
                    @trier-alphabet="trierAlphabet"
                    @update="onComponentShowRequest"/>
            </Suspense>
        </div>
    </div>
</template>
