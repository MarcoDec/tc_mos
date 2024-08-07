<script setup>
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {computed, ref, onErrorCaptured} from 'vue'
    import {useNomenclatureStore} from '../../../../../stores/project/product/nomenclatures'
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

    const addProductItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        subProduct: [],
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const updateProductItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        subProduct: [],
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const addComponentItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        component: [],
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const updateComponentItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        component: [],
        mandated: false,
        product: `/api/products/${idProduct}`
    })

    const addEquivalentItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        componentEquivalent: [],
        mandated: false,
        product: `/api/products/${idProduct}`
    })
    const updateEquivalentItem = ref({
        quantity: {
            code: '/api/units/1',
            value: 1
        },
        component: [],
        mandated: false,
        product: `/api/products/${idProduct}`,
        equivalent: []
    })

    const AddProductForm = ref(false)
    const addProductFormField = ref([])
    const ProductUpdateForm = ref(false)
    const updateProductFormField = ref([])

    const AddComponentForm = ref(false)
    const addComponentFormField = ref([])
    const ComponentUpdateForm = ref(false)
    const updateComponentFormField = ref([])

    const AddEquivalentForm = ref(false)
    const addEquivalentFormField = ref([])
    const EquivalentUpdateForm = ref(false)
    const updateEquivalentFormField = ref([])

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
            label: 'Groupe Equivalence',
            name: 'equivalent',
            type: 'multiselect-fetch',
            api: '/api/component-equivalents',
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
    updateProductFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Sous-produit',
            name: 'subProduct',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1,
            readonly: true
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
    updateComponentFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Composant',
            name: 'component',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1,
            readonly: true
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
    addEquivalentFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Equivalent',
            name: 'equivalent',
            type: 'multiselect-fetch',
            api: '/api/component-equivalents',
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
    updateEquivalentFormField.value = [
        {
            label: 'Mandat',
            name: 'mandated',
            type: 'boolean'
        },
        {
            label: 'Equivalent',
            name: 'equivalent',
            type: 'multiselect-fetch',
            api: '/api/component-equivalents',
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

    const min = computed(() => AddComponentForm.value || ComponentUpdateForm.value || AddProductForm.value || ProductUpdateForm.value || AddEquivalentForm.value || EquivalentUpdateForm.value)
    const col1 = computed(() => {
        if (min.value) return 5
        return 12
    })
    async function refresh() {
        await nomenclatureStore.fetchAll(nomenclatureFetchCriteria.getFetchCriteria)
        itemsTable.value = nomenclatureStore.nomenclatures
    }
    function ajouteProduit() {
        AddProductForm.value = !AddProductForm.value
        ProductUpdateForm.value = false

        AddComponentForm.value = false
        ComponentUpdateForm.value = false

        AddEquivalentForm.value = false
        EquivalentUpdateForm.value = false
    }
    function ajouteComposant() {
        AddProductForm.value = false
        ProductUpdateForm.value = false

        AddComponentForm.value = !AddComponentForm.value
        ComponentUpdateForm.value = false

        AddEquivalentForm.value = false
        EquivalentUpdateForm.value = false
    }
    function ajouteEquivalent() {
        AddProductForm.value = false
        ProductUpdateForm.value = false

        AddComponentForm.value = false
        ComponentUpdateForm.value = false

        AddEquivalentForm.value = !AddEquivalentForm.value
        EquivalentUpdateForm.value = false
    }
    function cancelAddForm() {
        AddProductForm.value = false
        ProductUpdateForm.value = false

        AddComponentForm.value = false
        ComponentUpdateForm.value = false

        AddEquivalentForm.value = false
        EquivalentUpdateForm.value = false
    }
    async function onAddSubmit() {
        AddProductForm.value = false
        ProductUpdateForm.value = false

        AddComponentForm.value = false
        ComponentUpdateForm.value = false

        AddEquivalentForm.value = false
        EquivalentUpdateForm.value = false
        await refresh()
    }
    isLoaded.value = true

    async function onCancelSearch() {
        nomenclatureFetchCriteria.resetAllFilter()
        await refresh()
    }
    async function onSearch(data) {
        isLoaded.value = false
        nomenclatureFetchCriteria.resetAllFilter()
        if (data.subProduct) nomenclatureFetchCriteria.addFilter('subProduct', data.subProduct[0])
        if (data.component) nomenclatureFetchCriteria.addFilter('component', data.component[0])
        if (data.equivalent) nomenclatureFetchCriteria.addFilter('equivalent', data.equivalent[0])
        if (typeof data.mandated !== 'undefined') nomenclatureFetchCriteria.addFilter('mandated', data.mandated)
        if (data.quantity && data.quantity.code) nomenclatureFetchCriteria.addFilter('quantity.code', optionsUnit.value.label(data.quantity.code))
        if (data.quantity && data.quantity.value) nomenclatureFetchCriteria.addFilter('quantity.value', data.quantity.value)
        await refresh()
        isLoaded.value = true
    }
    async function updateItem(item) {
        isLoaded.value = false
        if (item.component === null && item.equivalent === null) {
            // affichage formulaire update Sous-Produit
            updateProductItem.value = {...updateProductItem.value, ...item}
            updateProductItem.value.subProduct = [item.subProduct['@id']]
            ProductUpdateForm.value = true
        } else if (item.subProduct === null && item.equivalent === null) {
            // affichage formulaire update Composant
            updateComponentItem.value = {...updateComponentItem.value, ...item}
            updateComponentItem.value.component = [item.component['@id']]
            ComponentUpdateForm.value = true
        } else if (item.subProduct === null && item.component === null) {
            // affichage formulaire update Groupe d'équivalence
            updateEquivalentItem.value = {...updateEquivalentItem.value, ...item}
            updateEquivalentItem.value.equivalent = [item.equivalent['@id']]
            EquivalentUpdateForm.value = true
        }
        await refresh()
        isLoaded.value = true
    }
    async function deleteItem(item) {
        isLoaded.value = false
        await nomenclatureStore.remove(item)
        await refresh()
        isLoaded.value = true
    }
    async function getPage(nPage) {
        nomenclatureFetchCriteria.gotoPage(Number(nPage))
        await refresh()
    }
    function trierAlphabet(data) {
        console.log('trierAlphabet', data)
    }
    const error = ref(null)

    const addProductKey = ref(0)
    const updateProductKey = ref(0)

    const addComponentKey = ref(0)
    const updateComponentKey = ref(0)

    const addEquivalentKey = ref(0)
    const updateEquivalentKey = ref(0)
    function errorCaptured(err, instance, info) {
        error.value = err
        console.debug('Une erreur a été capturée:', err, instance, info)
        addProductKey.value++
        updateProductKey.value++

        addComponentKey.value++
        updateComponentKey.value++

        addEquivalentKey.value++
        updateEquivalentKey.value++
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
                <span class="ml-10">
                    <AppBtn variant="success" label="Ajout" @click="ajouteEquivalent">
                        <Fa icon="plus"/>
                        Ajouter un groupe d'équivalence Composant
                    </AppBtn>
                </span>
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
                            :key="`addProduct${addProductKey}`"
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
                            :key="`addComponent${addComponentKey}`"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addProductComponent"
                            :fields="addComponentFormField"
                            :model-value="addComponentItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && AddEquivalentForm"
                            id="addEquivalent"
                            :key="`addEquivalent${addEquivalentKey}`"
                            api-method="POST"
                            api-url="/api/nomenclatures"
                            form="addEquivalentForm"
                            :fields="addEquivalentFormField"
                            :model-value="addEquivalentItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && ProductUpdateForm"
                            id="updateProduct"
                            :key="`updateProduct${updateProductKey}`"
                            api-method="PATCH"
                            api-url=""
                            card-title="Modifier la composition en sous-produit"
                            form="updateProductForm"
                            :fields="updateProductFormField"
                            :model-value="updateProductItem"
                            @cancel="cancelAddForm"
                            @submitted="onAddSubmit"/>
                        <InlistAddForm
                            v-if="isLoaded && EquivalentUpdateForm"
                            id="updateEquivalent"
                            api-method="PATCH"
                            api-url=""
                            card-title="Modifier la composition en groupe d'équivalence"
                            form="updateEquivalentForm"
                            :fields="updateEquivalentFormField"
                            :model-value="updateEquivalentItem"
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
