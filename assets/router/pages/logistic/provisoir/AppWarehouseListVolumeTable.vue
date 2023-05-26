<script setup>
    import {computed, ref} from 'vue'
    import {useWarehouseVolumeListStore} from './warehouseVolumeList'

    //import {useWarehouseListItemsStore} from '../../../../stores/logistic/warehouses/warehouseListItems'

    // defineProps({
    //     icon: {required: true, type: String},
    //     title: {required: true, type: String}
    // })

    const roleuser = ref('reader')
    // let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const storeWarehouseVolumeList = useWarehouseVolumeListStore()
    await storeWarehouseVolumeList.fetch()
    const itemsTable = ref(storeWarehouseVolumeList.itemsWarehousesStock)
    const formData = ref({
        ref: null, quantite: null, type: null
    })
    const optionRef = [
        {text: 'CAB-1000', value: 100},
        {text: 'CAB-100', value: 10},
        {text: '1188481x', value: 1000},
        {text: '1188481', value: 10000},
        {text: 'ywx', value: 20}
    ]
    const fieldsForm = [
        {
            create: true,
            filter: true,
            label: 'Ref',
            name: 'ref',
            options: {label: value => optionRef.find(option => option.value === value)?.text ?? null, options: optionRef},
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const tabFields = [
        {
            create: true,
            filter: true,
            label: 'Ref',
            name: 'ref',
            options: {label: value => optionRef.find(option => option.value === value)?.text ?? null, options: optionRef},
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            ref: null,
            quantite: null,
            type: null
        }
        formData.value = itemsNull
    }
    async function ajoutWarehouseVolume(){
        const form = document.getElementById('addWarehouseStock')
        const formData1 = new FormData(form)
        const itemsAddData = {
            composant: formData1.get('composant'),
            produit: formData1.get('produit'),
            numeroDeSerie: formData1.get('numeroDeSerie'),
            localisation: formData1.get('localisation'),
            quantite: formData1.get('quantite'),
            prison: formData1.get('prison')
        }
        await storeWarehouseVolumeList.addWarehouseStock(itemsAddData)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesStock]
        AddForm.value = false
        updated.value = false
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            composant: null,
            produit: null,
            numeroDeSerie: null,
            localisation: null,
            quantite: null,
            prison: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            families: item.families
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeWarehouseVolumeList.delated(id)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesStock]
    }
    async function getPage(nPage){
        await storeWarehouseVolumeList.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
    }
    async function trierAlphabet(payload) {
        await storeWarehouseVolumeList.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            families: inputValues.families ?? '',
            name: inputValues.name ?? ''
        }
        await storeWarehouseVolumeList.filterBy(payload)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesStock]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = false
        await storeWarehouseVolumeList.fetch()
    }
</script>

<template>
    <AppCol class="d-flex justify-content-between mb-2">
        <!-- <h1>
            <Fa :icon="icon"/>
            {{ title }}
        </h1> -->
        <AppBtn variant="success" label="Ajout" @click="ajoute">
            <Fa icon="plus"/>
            Ajouter
        </AppBtn>
    </AppCol>
    <AppRow>
        <AppCol>
            <AppCardableTable
                :current-page="storeWarehouseVolumeList.currentPage"
                :fields="tabFields"
                :first-page="storeWarehouseVolumeList.firstPage"
                :items="itemsTable"
                :last-page="storeWarehouseVolumeList.lastPage"
                :min="AddForm"
                :next-page="storeWarehouseVolumeList.nextPage"
                :pag="storeWarehouseVolumeList.pagination"
                :previous-page="storeWarehouseVolumeList.previousPage"
                :user="roleuser"
                form="formWarehouseCardableTable"
                @update="update"
                @deleted="deleted"
                @get-page="getPage"
                @trier-alphabet="trierAlphabet"
                @search="search"
                @cancel-search="cancelSearch"/>
        </AppCol>
        <AppCol v-if="AddForm && !updated" class="col-7">
            <AppCard class="bg-blue col" title="">
                <AppRow>
                    <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable id="addWarehouseStock" :fields="fieldsForm" :model-value="formData" label-cols/>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouseVolume">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
