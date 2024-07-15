<script setup>
    import {computed, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import AppFormCardable from '../../../../../form-cardable/AppFormCardable'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    import useUser from '../../../../../../stores/security'

    const emit = defineEmits(['cancel', 'saved'])
    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    const user = useUser()
    const companyID = user.company.split('/').pop()
    // console.log('companyID', companyID)
    //endregion
    const isPopupVisible = ref(false)
    const itemsAddData = ref({})
    let addFormKey = 0
    let violations = []

    const fetchZones = useZonesStore()
    //region initialisation des champs pour le formulaire d'ajout d'une zone dans l'entrepôt

    const itemsNull = {
        name: null,
        warehouse: `/api/warehouses/${warehouseId}`,
        company: `/api/companies/${companyID}`
    }
    const localAddFormData = ref(itemsNull)
    //region établissement de la liste des champs en computed
    const commonAddFormFields = [
        {
            label: 'Nom de la zone',
            name: 'name',
            type: 'text'
        }
    ]

    const fieldsForm = computed(() => commonAddFormFields)
    //endregion
    //region définition des fonctions associées au formulaire d'ajout d'un stock

    function addFormChange(data) {
        //survient la plupart du temps lorsqu'on modifie les valeurs d'un input sans besoin de valider le formulaire ou de sortir du champ
        //Attention ne fonctionne pas pour les MultiSelect => Voir updatedSearch
        localAddFormData.value = data
        addFormKey++
    }
    async function updatedSearch(data) {
        inputValue.value[data.field.name] = data.data
    }

    async function ajoutWarehouseZone(){
        itemsAddData.value = {
            name: localAddFormData.value.name,
            warehouse: `/api/warehouses/${warehouseId}`,
            company: `/api/companies/${companyID}`
        }
        try {
            await fetchZones.postNewZone(itemsAddData.value)
            emit('saved')
        } catch (e) {
            alert(e.message)
            violations = fetchZones.violations
            isPopupVisible.value = true
        }
    }
    function annuleAddZone(){
        violations = []
        isPopupVisible.value = false
        localAddFormData.value = itemsNull
        emit('cancel')
    }
    //endregion
</script>

<template>
    <AppCard class="bg-blue col" title="">
        <AppRow>
            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annuleAddZone">
                <Fa icon="angle-double-left"/>
            </button>
            <h4 class="col">
                <Fa icon="plus"/> Ajout
            </h4>
        </AppRow>
        <br/>
        <AppFormCardable
            id="addWarehouseZone"
            :key="addFormKey"
            :fields="fieldsForm"
            :model-value="localAddFormData"
            label-cols
            @update:model-value="addFormChange"
            @search-change="updatedSearch"/>
        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
            <div v-for="violation in violations" :key="violation">
                <li>{{ violation.message }}</li>
            </div>
        </div>
        <AppCol class="btnright">
            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouseZone">
                <Fa icon="plus"/> Ajouter
            </AppBtn>
        </AppCol>
    </AppCard>
</template>
