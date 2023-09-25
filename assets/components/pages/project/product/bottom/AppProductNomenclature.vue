<script setup>
    import {computed, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../AppSuspense.vue'
    import {getOptions} from '../../../../../utils'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useNomenclatureStore} from '../../../../../stores/project/product/nomenclatures'

    //import AppSuspense from '../../../../AppSuspense.vue'
    import AppCardableTable from '../../../../bootstrap-5/app-cardable-collection-table/AppCardableTable.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'

    const isLoaded = ref(false)

    const route = useRoute()
    const idProduct = Number(route.params.id_product)

    const nomenclatureStore = useNomenclatureStore()
    const nomenclatureFetchCriteria = useFetchCriteria('nomenclatures')
    nomenclatureFetchCriteria.addFilter('product', `/api/products/${idProduct}`)
    await nomenclatureStore.fetchAll(nomenclatureFetchCriteria.getFetchCriteria)
    console.log('nomenclature', nomenclatureStore.nomenclatures)

    const productsOptions = ref([])
    const productStore = useProductStore()
    const productFormFetchCriteria = useFetchCriteria('productForms')
    productFormFetchCriteria.addFilter('pagination', 'false')
    await productStore.fetchAll(productFormFetchCriteria.getFetchCriteria)
    productsOptions.value = getOptions(productStore.products, 'code')

    const AddProductForm = ref(false)
    const addProductFormField = ref([])
    const ProductUpdateForm = ref(false)
    const updateProductFormField = ref([])

    const componentsOptions = ref([])
    const componentStore = useComponentListStore()
    const componentFormFetchCriteria = useFetchCriteria('componentForms')
    componentFormFetchCriteria.addFilter('pagination', 'false')
    await componentStore.fetchAll(componentFormFetchCriteria.getFetchCriteria)
    componentsOptions.value = getOptions(componentStore.components, 'code')

    const AddComponentForm = ref(false)
    const addComponentFormField = ref([])
    const ComponentUpdateForm = ref(false)
    const updateComponentFormField = ref([])

    addProductFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Produit',
            name: 'product',
            options: productsOptions.value,
            type: 'select'
        },
        {
            label: 'Quantité',
            name: 'quantity',
            measure: {
                code: {
                    label: 'Code',
                    name: 'code',
                    type: 'text'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number'
                }
            },
            type: 'measure'
        }
    ]
    const col1 = computed(() => {
        if (AddComponentForm.value || ProductUpdateForm.value || ComponentUpdateForm.value || AddProductForm.value) return 5
        return 12
    })
    async function refresh() {
        console.log('refresh Table')
    }
    function ajouteProduit() {
        AddProductForm.value = true
        AddComponentForm.value = false
        ProductUpdateForm.value = false
        ComponentUpdateForm.value = false
    }
    function ajouteComposant() {
        AddComponentForm.value = true
        AddProductForm.value = false
        ProductUpdateForm.value = false
        ComponentUpdateForm.value = false
    }
    function cancelAddForm() {
        AddComponentForm.value = false
        AddProductForm.value = false
        ProductUpdateForm.value = false
        ComponentUpdateForm.value = false
    }
    async function onAddSubmit() {
        AddComponentForm.value = false
        AddProductForm.value = false
        ProductUpdateForm.value = false
        ComponentUpdateForm.value = false
        await refresh()
    }
    isLoaded.value = true
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol class="mb-2">
                <span class="ml-10">
                    <AppBtn variant="success" label="Ajout" @click="ajouteProduit">
                        <Fa icon="plus"/>
                        Ajouter un nouveau produit
                    </AppBtn>
                </span>
                <span class="ml-10">
                    <AppBtn variant="success" label="Ajout" @click="ajouteComposant">
                        <Fa icon="plus"/>
                        Ajouter un nouveau Composant
                    </AppBtn>
                </span>
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol :cols="col1">
                <AppCardableTable
                    v-if="isLoaded"
                    :current-page="storeEmployeeListFormation.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListFormation.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListFormation.lastPage"
                    :min="AddForm || updated"
                    :next-page="storeEmployeeListFormation.nextPage"
                    :pag="storeEmployeeListFormation.pagination"
                    :previous-page="storeEmployeeListFormation.previousPage"
                    :user="roleuser"
                    form="formEmployeeFormationCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <AppCol :cols="12 - col1">
                <AppRow>
                    <AppSuspense>
                        <InlistAddForm
                            v-if="isLoaded && AddProductForm"
                            id="addProduct"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addEmployeeSkillForm"
                            :fields="addProductFormField"
                            :model-value="addProductItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && AddComponentForm"
                            id="addComponent"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addEmployeeSkillForm"
                            :fields="addComponentFormField"
                            :model-value="addComponentItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && ProductUpdateForm"
                            id="updateProduct"
                            api-method="PATCH"
                            api-url=""
                            card-title="Modifier la composition en sous-produit"
                            form="updateProductForm"
                            :fields="updateProductFormField"
                            :model-value="updateProductItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && ComponentUpdateForm"
                            id="updateComponent"
                            api-method="PATCH"
                            api-url=""
                            card-title="Modifier la composition en composant"
                            form="updateComponentForm"
                            :fields="updateComponentFormField"
                            :model-value="updateComponentItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                    </AppSuspense>
                </AppRow>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
   .ml-10 {
       margin-left: 10px;
   }
</style>
