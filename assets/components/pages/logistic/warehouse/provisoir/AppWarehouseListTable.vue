<script setup>
    import {computed, ref} from 'vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    import Fa from '../../../../Fa'
    import {useWarehouseListStore} from './warehouseList'
    import {useRouter} from 'vue-router'
    import {useWarehouseShowStore} from '../../../../../stores/logistic/warehouses/warehouseShow'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const router = useRouter()
    const storeWarehouseShow = useWarehouseShowStore()

    const storeWarehouseList = useWarehouseListStore()
    await storeWarehouseList.fetch()
    const itemsTable = ref(storeWarehouseList.itemsWarehouses)
    const formData = ref({
        families: null, name: null
    })
    const fieldsForm = [
        {label: 'Nom *', name: 'name', type: 'text'},
        {
            label: 'Famille ',
            name: 'families',
            options: [
                {
                    disabled: false,
                    label: 'Prison',
                    value: 'prison'
                },
                {
                    disabled: false,
                    label: 'Production',
                    value: 'production'
                },
                {
                    disabled: false,
                    label: 'Réception',
                    value: 'réception'
                },
                {
                    disabled: false,
                    label: 'Magasin pièces finies',
                    value: 'magasin pièces finies'
                },
                {
                    disabled: false,
                    label: 'Expédition',
                    value: 'expédition'
                },
                {
                    disabled: false,
                    label: 'Magasin matières premières',
                    value: 'magasin matières premières'
                },
                {
                    disabled: false,
                    label: 'Camion',
                    value: 'camion'
                }
            ],
            optionsList: {
                camion: 'camion',
                expédition: 'expédition',
                'magasin matières premières': 'magasin matières premières',
                'magasin pièces finies': 'magasin pièces finies',
                prison: 'prison',
                production: 'production',
                réception: 'réception'
            },
            type: 'multiselect'
        }
    ]

    const tabFields = [
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {
            label: 'Famille',
            min: true,
            name: 'families',
            options: [
                {
                    disabled: false,
                    label: 'Prison',
                    value: 'prison'
                },
                {
                    disabled: false,
                    label: 'Production',
                    value: 'production'
                },
                {
                    disabled: false,
                    label: 'Réception',
                    value: 'réception'
                },
                {
                    disabled: false,
                    label: 'Magasin pièces finies',
                    value: 'magasin pièces finies'
                },
                {
                    disabled: false,
                    label: 'Expédition',
                    value: 'expédition'
                },
                {
                    disabled: false,
                    label: 'Magasin matières premières',
                    value: 'magasin matières premières'
                },
                {
                    disabled: false,
                    label: 'Camion',
                    value: 'camion'
                }
            ],
            optionsList: {
                camion: 'camion',
                expédition: 'expédition',
                'magasin matières premières': 'magasin matières premières',
                'magasin pièces finies': 'magasin pièces finies',
                prison: 'prison',
                production: 'production',
                réception: 'réception'
            },
            type: 'multiselect'
        }
    ]
    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            families: null
        }
        formData.value = itemsNull
    }
    async function ajoutWarehouse(){
        const form = document.getElementById('addWarehouse')
        const formData1 = new FormData(form)

        if (typeof formData.value.families !== 'undefined') {
            formData.value.families = JSON.parse(JSON.stringify(formData.value.families))
        }

        const itemsAddData = {
            name: formData1.get('name'),
            families: formData.value.families
        }

        violations = await storeWarehouseList.addWarehouse(itemsAddData)

        if (violations.length > 0){
            isPopupVisible.value = true
        } else {
            AddForm.value = false
            updated.value = false
            isPopupVisible.value = false
            itemsTable.value = [...storeWarehouseList.itemsWarehouses]
        }
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            families: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }

    function update(item) {
        storeWarehouseShow.setCurrentId(item.id)
        // updated.value = true
        // AddForm.value = true
        // const itemsData = {
        //     families: item.families
        // }
        // formData.value = itemsData
        router.push(`warehouse/${item.id}`)
    }

    async function deleted(id){
        await storeWarehouseList.delated(id)
        itemsTable.value = [...storeWarehouseList.itemsWarehouses]
    }
    async function getPage(nPage){
        console.log(nPage)
        await storeWarehouseList.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeWarehouseList.itemsWarehouses]
    }
    async function trierAlphabet(payload) {
        await storeWarehouseList.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            families: inputValues.families ?? '',
            name: inputValues.name ?? ''
        }
        await storeWarehouseList.filterBy(payload)
        itemsTable.value = [...storeWarehouseList.itemsWarehouses]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = false
        await storeWarehouseList.fetch()
    }

    function input(v){
        formData.value.families = v.families
    }
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>
                    <Fa :icon="icon"/>
                    {{ title }}
                    <span class="btn-float-right">
                        <AppBtn variant="success" label="Ajout" @click="ajoute">
                            <Fa icon="plus"/>
                            Ajouter
                        </AppBtn>
                    </span>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <AppSuspense>
                    <AppCardableTable
                        :current-page="storeWarehouseList.currentPage"
                        :fields="tabFields"
                        :first-page="storeWarehouseList.firstPage"
                        :items="itemsTable"
                        :last-page="storeWarehouseList.lastPage"
                        :min="AddForm"
                        :next-page="storeWarehouseList.nextPage"
                        :pag="storeWarehouseList.pagination"
                        :previous-page="storeWarehouseList.previousPage"
                        :user="roleuser"
                        form="formWarehouseCardableTable"
                        @update="update"
                        @deleted="deleted"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @search="search"
                        @cancel-search="cancelSearch"/>
                </AppSuspense>
            </div>
            <div v-show="AddForm" class="col-7">
                <AppSuspense>
                    <AppCard class="bg-blue col" title="">
                        <div class="row">
                            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                                <Fa icon="angle-double-left"/>
                            </button>
                            <h4 class="col">
                                <Fa icon="plus"/> Ajout d'un nouvel entrepôt
                            </h4>
                        </div>
                        <AppSuspense><AppFormCardable id="addWarehouse" :fields="fieldsForm" label-cols @update:model-value="input"/></AppSuspense>
                        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                            <div v-for="violation in violations" :key="violation">
                                <li>{{ violation.message }}</li>
                            </div>
                        </div>
                        <div class="btnright row">
                            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouse">
                                <Fa icon="plus"/> Enregister
                            </AppBtn>
                        </div>
                    </AppCard>
                </AppSuspense>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
