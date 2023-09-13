<script setup>
    import {computed, ref} from 'vue'
    import useOptions from '../../../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useStockListStore} from '../../../../../../stores/logistic/stocks/stocks'
    import AppFormCardable from '../../../../../form-cardable/AppFormCardable'
    import AppSuspense from '../../../../../AppSuspense.vue'

    const emit = defineEmits(['cancel', 'saved'])
    const props = defineProps({
        item: {type: Object, required: true}
    })
    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    const isPopupVisible = ref(false)
    //region initialisation de la variable locale
    const itemsUpdateData = ref({})
    const localFormData = ref(props.item)
    //endregion
    let formKey = 0
    let violations = []
    const fetchUnits = useOptions('units')
    await fetchUnits.fetchOp()
    const optionsUnit = fetchUnits.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    const fetchStocks = useStockListStore()
    //region initialisation des champs pour le formulaire d'ajout d'un stock
    //region établissement de la liste des champs en computed
    const commonAddFormFields = [
        {
            label: 'Numéro de série',
            name: 'batchNumber',
            type: 'text'
        },
        {
            label: 'Localisation',
            name: 'location',
            type: 'text'
        },
        {
            label: 'Prison',
            name: 'jail',
            type: 'boolean'
        }
    ]
    const specificComponentUpdateFormFields = computed(() => [
        {
            label: 'Quantité ',
            name: 'quantity',
            measure: {
                code: { //récupérer l'unité du composant sélectionné
                    label: 'Unité',
                    name: 'code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number'
                }
            },
            type: 'measure'
        }
    ])
    const specificProductUpdateFormFields = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            options: {label: value => optionProduit.value.find(option => option.value === value)?.text ?? null, options: optionProduit.value},
            type: 'multiselect',
            max: 1
        },
        {
            label: 'Quantité ',
            name: 'quantity',
            measure: {
                code: { // Mettre U en permanence par défault
                    label: 'Unité',
                    name: 'code',
                    type: 'text',
                    readonly: true
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number'
                }
            },
            type: 'measure'
        }
    ])
    const fieldsForm = computed(() => {
        if (localFormData.value.itemType === true) return [...commonAddFormFields, ...specificProductUpdateFormFields.value]
        return [...commonAddFormFields, ...specificComponentUpdateFormFields.value]
    })
    //endregion
    //region définition des fonctions associées au formulaire d'ajout d'un stock

    function updateFormChange(data) {
        //survient la plupart du temps lorsqu'on modifie les valeurs d'un input sans besoin de valider le formulaire ou de sortir du champ
        //Attention ne fonctionne pas pour les MultiSelect => Voir updatedSearch
        if (data.itemType === !localFormData.value.itemType) {
            switch (data.itemType) {
                case true:
                    localFormData.value.quantity = {code: 'U', value: data.quantity.value}
                    localFormData.value.component = null
                    localFormData.value.itemType = true
                    break
                default:
                    localFormData.value.quantity = {code: '', value: data.quantity.value}
                    localFormData.value.product = null
                    localFormData.value.itemType = false
            }
            formKey++
        } else {
            localFormData.value = data
        }
    }

    async function updatedWarehouseStock(){
        itemsUpdateData.value = {
            batchNumber: localFormData.value.batchNumber,
            location: localFormData.value.location,
            quantity: {code: localFormData.value.quantity.codeLabel, value: localFormData.value.quantity.value},
            jail: localFormData.value.jail
        }
        itemsUpdateData.value.warehouse = `/api/warehouses/${warehouseId}`
        if (localFormData.value.component) {
            itemsUpdateData.value.item = localFormData.value.component['@id']
        } else {
            itemsUpdateData.value.item = localFormData.value.product['@id']
        }
        try {
            await fetchStocks.updateStock(props.item.id, itemsUpdateData.value)
            emit('saved')
        } catch (e) {
            alert(e.message)
            violations = fetchStocks.violations
            isPopupVisible.value = true
        }
    }
    function annuleUpdateStock(){
        violations = []
        isPopupVisible.value = false
        emit('cancel')
    }
    //endregion
</script>

<template>
    <AppSuspense>
        <AppCard class="bg-blue col" title="">
            <AppRow>
                <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annuleUpdateStock">
                    <Fa icon="angle-double-left"/>
                </button>
                <h4 class="col">
                    <Fa icon="pencil"/> Modification du
                    <span v-if="item.component" :title="`Stock n° ${item.batchNumber}`">stock composant <span class="title-form">{{ item.component.code }} - {{ item.component.name }}</span></span>
                    <span v-else :title="`Stock n° ${item.batchNumber}`">stock produit <span class="title-form">{{ item.product.code }} - {{ item.product.name }}</span></span>
                </h4>
            </AppRow>
            <br/>
            <AppFormCardable
                id="updateWarehouseStock"
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
                <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="updatedWarehouseStock">
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
