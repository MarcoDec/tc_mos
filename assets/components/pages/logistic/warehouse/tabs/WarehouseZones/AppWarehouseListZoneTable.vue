<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../../../stores/fetch-criteria/fetchCriteria'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import WarehouseZoneAddForm from './WarehouseZoneAddForm.vue'
    import WarehouseZoneUpdateForm from './WarehouseZoneUpdateForm.vue'

    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const filterBy = computed(() => null)
    const filter = ref(false)
    const trierAlpha = computed(() => null)
    const sortable = ref(true)
    let updateKey = 0

    //region récupération des zones liées à l'entrepot
    const fetchZones = useZonesStore()
    const fetchCriteria = useFetchCriteria('warehouseZoneList')
    fetchZones.warehouseID = Number(warehouseId)
    fetchCriteria.addFilter('warehouse', `/api/warehouses/${warehouseId}`)
    await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
    //endregion
    //region définition des champs tableau et des variables locales de liste et de vecteur de recherche
    const itemsTable = ref([])
    itemsTable.value = fetchZones.zones
    const formData = ref({
        component: null, product: null, batchNumber: null, location: null, quantity: null, jail: null
    })
    const tabFields = [
        {
            create: true,
            filter: true,
            label: 'Zone',
            name: 'name',
            sort: true,
            type: 'string',
            update: true,
            min: true
        }
    ]
    //endregion
    //region fonctions de gestion de la liste des stocks
    function ajoute(){
        AddForm.value = true
        updated.value = false
    }
    function update(data) {
        formData.value = {
            id: data['@id']
        }
        if (updated.value === false) {
            updated.value = true
            AddForm.value = false
        } else {
            updateKey++
        }
    }
    async function updateListe() {
        await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
        itemsTable.value = fetchZones.zones
    }
    async function deleted(id){
        await fetchZones.deleteZone(`/api/zones/${id}`)
        await updateListe()
    }
    async function getPage(nPage){
        fetchCriteria.gotoPage(nPage)
        await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
        itemsTable.value = [...fetchZones.zones]
    }
    async function trierAlphabet(payload) {
        fetchCriteria.addSort(payload)
        await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
        sortable.value = true
        trierAlpha.value = payload
    }
    async function search(inputValues) {
        const payload = {
            warehouse: 'comp',
            zone: 'zone'
        }
        // console.log('inputValues', inputValues)
        if (inputValues.name) {
            fetchCriteria.addFilter('name', inputValues.name)
        }
        await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
        itemsTable.value = fetchZones.zones
        filter.value = true
        filterBy.value = payload
    }
    async function cancelSearch() {
        // console.log('cancelSearch')
        filter.value = true
        fetchCriteria.resetFilter('name')
        await fetchZones.fetchZones(fetchCriteria.getFetchCriteria)
        itemsTable.value = fetchZones.zones
    }
    //endregion
    async function afterCreation() {
        AddForm.value = false
        await updateListe()
    }
    async function afterUpdate() {
        updated.value = false
        await updateListe()
    }
</script>

<template>
    <AppRow>
        <AppCol class="d-flex justify-content-between mb-2">
            <span>
                <AppBtn variant="success" label="Ajout" @click="ajoute">
                    <Fa icon="plus"/>
                    Créer une nouvelle zone
                </AppBtn>
            </span>
        </AppCol>
    </AppRow>
    <AppRow>
        <AppCol>
            <AppSuspense>
                <AppCardableTable
                    :current-page="fetchZones.currentPage"
                    :fields="tabFields"
                    :first-page="fetchZones.firstPage"
                    :items="itemsTable"
                    :last-page="fetchZones.lastPage"
                    :min="AddForm || updated"
                    :next-page="fetchZones.nextPage"
                    :pag="fetchZones.pagination"
                    :previous-page="fetchZones.previousPage"
                    :user="roleuser"
                    form="formWarehouseStockTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppSuspense>
        </AppCol>
        <AppCol v-if="AddForm">
            <AppSuspense>
                <WarehouseZoneAddForm @cancel="AddForm = false" @saved="afterCreation"/>
            </AppSuspense>
        </AppCol>
        <AppCol v-if="updated">
            <WarehouseZoneUpdateForm :key="`update_${updateKey}`" :item="formData" @cancel="updated = false" @saved="afterUpdate"/>
        </AppCol>
    </AppRow>
</template>

<style scoped>
.btn-float-right{
    float: right;
}
</style>
