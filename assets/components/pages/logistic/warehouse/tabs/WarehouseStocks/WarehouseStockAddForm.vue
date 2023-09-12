<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../../stores/option/options'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useProductStore} from '../../../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import {useStockListStore} from '../../../../../../stores/logistic/stocks/stocks'
    import AppFormCardable from '../../../../../form-cardable/AppFormCardable'

    const emit = defineEmits(['cancel', 'saved'])
    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    const isPopupVisible = ref(false)
    const itemsAddData = ref({})
    let addFormKey = 0
    let violations = []
    const fetchUnits = useOptions('units')
    await fetchUnits.fetchOp()
    const optionsUnit = fetchUnits.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    //console.log('optionsUnit', optionsUnit)
    const fetchStocks = useStockListStore()
    //region initialisation des champs pour le formulaire d'ajout d'un stock
    //region champ multi-select composant
    const addStockFormComponentSearchCriteria = useFetchCriteria('addStockFormComponent')
    const fetchComponentStore = useComponentListStore()
    async function updateComponents() {
        await fetchComponentStore.fetchAll(addStockFormComponentSearchCriteria.getFetchCriteria)
        return fetchComponentStore.components
    }
    await updateComponents()
    const optionComposant = computed(() => fetchComponentStore.components.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    //region champ multi-select produit
    const addStockFormProductSearchCriteria = useFetchCriteria('addStockFormProduct')
    const fetchProductStore = useProductStore()
    async function updateProducts() {
        await fetchProductStore.fetchAll(addStockFormProductSearchCriteria.getFetchCriteria)
        return fetchProductStore.products
    }
    await updateProducts()
    const optionProduit = computed(() => fetchProductStore.products.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    const itemsNull = {
        itemType: false,
        batchNumber: null,
        jail: false,
        location: null,
        component: null,
        product: null,
        quantity: {
            code: 'U',
            value: 0
        }
    }
    const localAddFormData = ref(itemsNull)
    //region établissement de la liste des champs en computed
    const commonAddFormFields = [
        {
            label: 'Composant(0)/Produit(1)',
            name: 'itemType',
            type: 'boolean'
        },
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
    const specificComponentAddFormFields = computed(() => [
        {
            label: 'Composant',
            name: 'component',
            options: {label: value => optionComposant.value.find(option => option.value === value)?.text ?? null, options: optionComposant.value},
            type: 'multiselect',
            max: 1
        },
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
    const specificProductAddFormFields = computed(() => [
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
        if (localAddFormData.value.itemType === true) return [...commonAddFormFields, ...specificProductAddFormFields.value]
        return [...commonAddFormFields, ...specificComponentAddFormFields.value]
    })
    //endregion
    //region définition des fonctions associées au formulaire d'ajout d'un stock

    function addFormChange(data) {
        //survient la plupart du temps lorsqu'on modifie les valeurs d'un input sans besoin de valider le formulaire ou de sortir du champ
        //Attention ne fonctionne pas pour les MultiSelect => Voir updatedSearch
        // console.log('addFormChange', localAddFormData.value, data)
        if (data.itemType === !localAddFormData.value.itemType) {
            switch (data.itemType) {
                case true:
                    localAddFormData.value.quantity = {code: 'U', value: data.quantity.value}
                    localAddFormData.value.component = null
                    localAddFormData.value.itemType = true
                    break
                default:
                    localAddFormData.value.quantity = {code: '', value: data.quantity.value}
                    localAddFormData.value.product = null
                    localAddFormData.value.itemType = false
            }
            //console.log('changement type d\'item', localAddFormData.value)
            addFormKey++
        } else {
            localAddFormData.value = data
        }
    }
    async function updatedSearch(data) {
        //console.log('updatedSearch', data)
        //inputValue.value[data.field.name]=data.data
        switch (data.field.name) {
            case 'component':
                addStockFormComponentSearchCriteria.addFilter('code', data.data)
                //console.log(addStockFormComponentSearchCriteria.getFetchCriteria)
                await updateComponents()
                break
            case 'product':
                addStockFormProductSearchCriteria.addFilter('code', data.data)
                //console.log(addStockFormProductSearchCriteria.getFetchCriteria)
                await updateProducts()
                break
            default:
            //nothing
        }
    }

    async function ajoutWarehouseStock(){
        // console.log(localAddFormData.value)
        itemsAddData.value = {
            batchNumber: localAddFormData.value.batchNumber,
            location: localAddFormData.value.location,
            quantity: {code: localAddFormData.value.quantity.code, value: localAddFormData.value.quantity.value},
            jail: localAddFormData.value.jail
        }
        switch (localAddFormData.value.itemType) {
            case false:
                //Ajout d'un stock composant en base
                console.log('composant', fetchUnits.options)
                itemsAddData.value.item = localAddFormData.value.component[0] ?? null
                itemsAddData.value.product = null
                itemsAddData.value.quantity = {
                    code: fetchUnits.find(localAddFormData.value.quantity.code)?.code,
                    value: localAddFormData.value.quantity.value
                }
                break
            default:
                console.log('product')
                //Ajout d'un stock produit en base
                itemsAddData.value.component = null
                itemsAddData.value.item = localAddFormData.value.product[0] ?? null
                itemsAddData.value.quantity = {
                    code: localAddFormData.value.quantity.code,
                    value: localAddFormData.value.quantity.value
                }
        }
        itemsAddData.value.warehouse = `/api/warehouses/${warehouseId}`
        // console.log('data to send', itemsAddData.value)
        try {
            await fetchStocks.addStock(itemsAddData.value)
            emit('saved')
        } catch (e) {
            alert(e.message)
            violations = fetchStocks.violations
            isPopupVisible.value = true
        }
    }
    function annuleAddStock(){
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
            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annuleAddStock">
                <Fa icon="angle-double-left"/>
            </button>
            <h4 class="col">
                <Fa icon="plus"/> Ajout
            </h4>
        </AppRow>
        <br/>
        <AppFormCardable
            id="addWarehouseStock"
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
            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouseStock">
                <Fa icon="plus"/> Ajouter
            </AppBtn>
        </AppCol>
    </AppCard>
</template>
