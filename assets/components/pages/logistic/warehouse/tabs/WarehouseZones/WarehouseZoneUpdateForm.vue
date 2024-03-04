<script setup>
import {computed, ref} from 'vue'
import {useRoute} from 'vue-router'
import AppFormCardable from '../../../../../form-cardable/AppFormCardable'
import AppSuspense from '../../../../../AppSuspense.vue'
import useZonesStore from '../../../../../../stores/production/company/zones'

const emit = defineEmits(['cancel', 'saved'])
const props = defineProps({
    item: {type: Object, required: true}
})
// console.log('props.item', props.item)

//region récupération des informations de route
const maRoute = useRoute()
const warehouseId = maRoute.params.id_warehouse
//endregion
const isPopupVisible = ref(false)
//region initialisation de la variable locale
const itemsUpdateData = ref({})
const localFormData = ref({})
//endregion
let formKey = 0
let violations = []
const fetchZones = useZonesStore()
const currentZone = ref({})
fetchZones.getZone(props.item.id).then(() => {
    currentZone.value = fetchZones.zone
    localFormData.value = {
        name: currentZone.value.name
    }
    console.log('currentZone name', currentZone.value.name)
})
//region initialisation des champs pour le formulaire d'ajout d'un stock
//region établissement de la liste des champs en computed
const commonAddFormFields = [
    {
        label: 'Nouveau nom de la zone',
        name: 'name',
        type: 'text'
    }
]

const fieldsForm = computed(() => commonAddFormFields)
//endregion
//region définition des fonctions associées au formulaire d'ajout d'un stock

function updateFormChange(data) {
    localFormData.value = data
    formKey++
}

async function updatedWarehouseZone(){
    itemsUpdateData.value = {
        name: localFormData.value.name
    }
    // itemsUpdateData.value.warehouse = `/api/warehouses/${warehouseId}`
    try {
        await fetchZones.patchZone(props.item.id, itemsUpdateData.value)
        emit('saved')
    } catch (e) {
        alert(e.message)
        violations = fetchZones.violations
        isPopupVisible.value = true
    }
}
function annuleUpdateZone(){
    violations = []
    isPopupVisible.value = false
    emit('cancel')
}
const currentName = computed(() => currentZone.value.name? currentZone.value.name : '')
//endregion
</script>

<template>
    <AppSuspense>
        <AppCard class="bg-blue col" title="">
            <AppRow>
                <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annuleUpdateZone">
                    <Fa icon="angle-double-left"/>
                </button>
                <h4 class="col">
                    <Fa icon="pencil"/> Modification de la zone "{{ currentName }}"
                </h4>
            </AppRow>
            <br/>
            <AppFormCardable
                id="updateWarehouseZone"
                :key="formKey"
                :fields="fieldsForm"
                :model-value="localFormData"
                label-cols
                @update:model-value="updateFormChange"/>
            <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <AppCol class="btnright">
                <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="updatedWarehouseZone">
                    <Fa icon="plus"/> Enregistrer
                </AppBtn>
            </AppCol>
        </AppCard>
    </AppSuspense>
</template>

<style scoped>
.title-form {
    border-bottom: 2px solid black;
}
</style>
