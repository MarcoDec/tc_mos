<script setup>
    // import AppCardableTable from '../../../../bootstrap-5/app-cardable-collection-table/AppCardableTable.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {computed, ref, onErrorCaptured} from 'vue'
    import {getOptions} from '../../../../../utils'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import {useNomenclatureStore} from '../../../../../stores/project/product/nomenclatures'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import useOptions from '../../../../../stores/option/options'

    const isLoaded = ref(false)

    const route = useRoute()
    const idProduct = Number(route.params.id_product)

    const roleuser = ref('reader')
    const fetchUnitOptions = useOptions('units')
    await fetchUnitOptions.fetchOp()
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const tabFields = ref([])
    const itemsTable = ref([])
    const nomenclatureStore = useNomenclatureStore()
    const nomenclatureFetchCriteria = useFetchCriteria('nomenclatures')
    nomenclatureFetchCriteria.addFilter('product', `/api/products/${idProduct}`)
    await nomenclatureStore.fetchAll(nomenclatureFetchCriteria.getFetchCriteria)
    itemsTable.value = nomenclatureStore.nomenclatures

    const productsOptions = ref([])
    const productStore = useProductStore()
    const productFormFetchCriteria = useFetchCriteria('productForms')
    productFormFetchCriteria.addFilter('pagination', 'false')
    await productStore.fetchAll(productFormFetchCriteria.getFetchCriteria)
    productsOptions.value = getOptions(productStore.products, 'code')
    const addProductItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        subProduct: null,
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const addComponentItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        component: null,
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const AddProductForm = ref(false)
    const addProductFormField = ref([])
    const ProductUpdateForm = ref(false)
    // const updateProductFormField = ref([])

    const componentsOptions = ref([])
    const componentStore = useComponentListStore()
    const componentFormFetchCriteria = useFetchCriteria('componentForms')
    await componentStore.fetchAll(componentFormFetchCriteria.getFetchCriteria)
    componentsOptions.value = getOptions(componentStore.components, 'code')

    const AddComponentForm = ref(false)
    const addComponentFormField = ref([])
    const ComponentUpdateForm = ref(false)
    // const updateComponentFormField = ref([])

    tabFields.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean',
            min: false
        },
        {
            label: 'Sous-Produit',
            name: 'subProduct',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            min: true,
            max: 1
        },
        {
            label: 'Composant',
            name: 'component',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            min: true,
            max: 1
        },
        {
            label: 'Quantité',
            name: 'quantity',
            filter: false,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number',
                    step: 0.01
                }
            },
            type: 'measure'
        }
    ]

    addProductFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Sous-produit',
            name: 'subProduct',
            //options: productsOptions.value,
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité',
            name: 'quantity',
            measure: {
                code: {
                    label: 'Code',
                    name: 'code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number',
                    step: 1
                }
            },
            type: 'measure'
        }
    ]
    addComponentFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Composant',
            name: 'component',
            //options: productsOptions.value,
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité',
            name: 'quantity',
            measure: {
                code: {
                    label: 'Code',
                    name: 'code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number',
                    step: 1
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
        await nomenclatureStore.fetchAll(nomenclatureFetchCriteria.getFetchCriteria)
        itemsTable.value = nomenclatureStore.nomenclatures
    }
    function ajouteProduit() {
        AddProductForm.value = !AddProductForm.value
        AddComponentForm.value = false
        ProductUpdateForm.value = false
        ComponentUpdateForm.value = false
    }
    function ajouteComposant() {
        AddComponentForm.value = !AddComponentForm.value
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
    const min = computed(() => AddComponentForm.value || AddProductForm.value || ProductUpdateForm.value || ComponentUpdateForm.value)
    async function onCancelSearch() {
        nomenclatureFetchCriteria.resetAllFilter()
        await refresh()
    }
    async function onSearch(data) {
        nomenclatureFetchCriteria.resetAllFilter()
        if (data.subProduct) nomenclatureFetchCriteria.addFilter('subProduct', data.subProduct[0])
        if (data.component) nomenclatureFetchCriteria.addFilter('component', data.component[0])
        if (typeof data.mandated !== 'undefined') nomenclatureFetchCriteria.addFilter('mandated', data.mandated)
        if (data.quantity && data.quantity.code) nomenclatureFetchCriteria.addFilter('quantity.code', optionsUnit.value.label(data.quantity.code))
        if (data.quantity && data.quantity.value) nomenclatureFetchCriteria.addFilter('quantity.value', data.quantity.value)
        await refresh()
    }
    function updateItem(item) {
        console.log('updateItem', item)
    }
    function deleteItem(item) {
        console.log('deleteItem', item)
    }
    async function getPage(nPage) {
        nomenclatureFetchCriteria.gotoPage(Number(nPage))
        await refresh()
        console.log('getPage', nPage)
    }
    function trierAlphabet(data) {
        console.log('trierAlphabet', data)
    }
    const error = ref(null)
    function errorCaptured(err, instance, info) {
        error.value = err
        console.error('Une erreur a été capturée:', err, instance, info)
        // Vous pouvez gérer l'erreur comme vous le souhaitez ici
        return false // Renvoie false pour éviter que l'erreur ne soit propagée plus haut.
    }
    onErrorCaptured((anError, compInst, errorInfo) => errorCaptured(anError, compInst, errorInfo))
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
                <!--                <span class="ml-10">-->
                <!--                    <AppBtn variant="success" label="Ajout" @click="ajouteComposant">-->
                <!--                        <Fa icon="plus"/>-->
                <!--                        Ajouter un groupe d'équivalence Composant-->
                <!--                    </AppBtn>-->
                <!--                </span>-->
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol :cols="col1">
                <AppCardableTable
                    v-if="isLoaded"
                    :current-page="`${nomenclatureStore.currentPage}`"
                    :fields="tabFields"
                    :first-page="`${nomenclatureStore.firstPage}`"
                    :items="itemsTable"
                    :last-page="`${nomenclatureStore.lastPage}`"
                    :min="min"
                    :next-page="`${nomenclatureStore.nextPage}`"
                    :pag="nomenclatureStore.pagination"
                    :previous-page="`${nomenclatureStore.previousPage}`"
                    :user="roleuser"
                    form="formEmployeeFormationCardableTable"
                    @update="updateItem"
                    @deleted="deleteItem"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="onSearch"
                    @cancel-search="onCancelSearch"/>
            </AppCol>
            <AppCol :cols="12 - col1">
                <AppRow>
                    <AppSuspense>
                        <InlistAddForm
                            v-if="isLoaded && AddProductForm"
                            id="addProduct"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addProductSubproduct"
                            :fields="addProductFormField"
                            :model-value="addProductItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && AddComponentForm"
                            id="addComponent"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addProductComponent"
                            :fields="addComponentFormField"
                            :model-value="addComponentItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <!--                        <InlistAddForm-->
                        <!--                            v-if="isLoaded && ProductUpdateForm"-->
                        <!--                            id="updateProduct"-->
                        <!--                            api-method="PATCH"-->
                        <!--                            api-url=""-->
                        <!--                            card-title="Modifier la composition en sous-produit"-->
                        <!--                            form="updateProductForm"-->
                        <!--                            :fields="updateProductFormField"-->
                        <!--                            :model-value="updateProductItem"-->
                        <!--                            @cancel="cancelAddForm"-->
                        <!--                            @submitted="onAddSubmit"/>-->
                        <!--                        <InlistAddForm-->
                        <!--                            v-if="isLoaded && ComponentUpdateForm"-->
                        <!--                            id="updateComponent"-->
                        <!--                            api-method="PATCH"-->
                        <!--                            api-url=""-->
                        <!--                            card-title="Modifier la composition en composant"-->
                        <!--                            form="updateComponentForm"-->
                        <!--                            :fields="updateComponentFormField"-->
                        <!--                            :model-value="updateComponentItem"-->
                        <!--                            @cancel="cancelAddForm"-->
                        <!--                            @submitted="onAddSubmit"/>-->
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
