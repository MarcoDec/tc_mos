<script setup>
    import {computed, ref} from 'vue'
    import {useWarehouseVolumeListStore} from './warehouseVolumeList'
    import {useRoute} from 'vue-router'

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

    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse

    const storeWarehouseVolumeList = useWarehouseVolumeListStore()
    storeWarehouseVolumeList.setIdWarehouse(warehouseId)
    await storeWarehouseVolumeList.fetch()
    const itemsTable = ref(storeWarehouseVolumeList.itemsWarehousesVolume)
    const formData = ref({
        ref: null, quantite: null, type: null
    })
    const optionRef = storeWarehouseVolumeList.getOptionRef
    const optionType = storeWarehouseVolumeList.getOptionType
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
            options: {label: value => optionType.find(option => option.value === value)?.text ?? null, options: optionType},
            sort: true,
            type: 'select',
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
            options: {label: value => optionType.find(option => option.value === value)?.text ?? null, options: optionType},
            sort: true,
            type: 'select',
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
        const form = document.getElementById('addWarehouseVolume')
        const formData1 = new FormData(form)
        const itemsAddData = {
            ref: formData1.get('ref'),
            quantite: formData1.get('quantite'),
            type: formData1.get('type')
        }
        await storeWarehouseVolumeList.addWarehouseVolume(itemsAddData)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesVolume]
        AddForm.value = false
        updated.value = false
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            ref: null,
            quantite: null,
            type: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            ref: item.ref,
            quantite: item.quantite,
            type: item.type

        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeWarehouseVolumeList.deleted(id)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesVolume]
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
        let reference = ''
        if (typeof inputValues.ref !== 'undefined'){
            reference = inputValues.ref
        }

        const payload = {
            ref: reference,
            quantite: inputValues.quantite ?? '',
            type: inputValues.type ?? ''
        }
        await storeWarehouseVolumeList.filterBy(payload)
        itemsTable.value = [...storeWarehouseVolumeList.itemsWarehousesVolume]
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
                <AppFormCardable id="addWarehouseVolume" :fields="fieldsForm" :model-value="formData" label-cols/>
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
