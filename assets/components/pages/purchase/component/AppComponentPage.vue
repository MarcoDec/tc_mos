<script setup>
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'
    import {computed, onMounted, ref} from 'vue'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../stores/option/options'
    import AppComponentCreate from './AppComponentCreate.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import useAttributesStore from '../../../../stores/attribute/attributes'
    import useColorsStore from '../../../../stores/color/colors'
    import useComponentAttributesStore from '../../../../stores/component/componentAttribute'
    import useComponentsStore from '../../../../stores/component/components'
    import useUnitsStore from '../../../../stores/unit/units'
    import useUser from '../../../../stores/security'
    import {useRouter} from 'vue-router'

    const title = 'Créer un Composant'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const router = useRouter()

    const StoreComponents = useComponentsStore()
    const fecthOptionsComponentFamilies = useOptions('component-families')

    const optionsComponentFamilies = computed(() =>
        fecthOptionsComponentFamilies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const StoreComponentAttributes = useComponentAttributesStore()
    const storeAttributes = useAttributesStore()

    const storeUnits = useUnitsStore()
    const storeColors = useColorsStore()

    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'draft', value: 'draft'},
        {text: 'to_validate', value: 'to_validate'},
        {text: 'warning', value: 'warning'}
    ]

    const fields = [
        // {
        //     label: 'Img',
        //     name: 'img',
        //     trie: false,
        //     type: 'img'
        // },
        {
            label: 'Référence',
            name: 'code',
            trie: true,
            type: 'text'
        },
        {
            label: 'Indice',
            name: 'index',
            trie: true,
            type: 'text'
        },
        {
            label: 'Désignation',
            name: 'name',
            trie: true,
            type: 'text'
        },
        {
            label: 'Famille',
            name: 'family',
            trie: false,
            type: 'select',
            options: {
                label: value =>
                    optionsComponentFamilies.value.find(option => option.value === value)?.text ?? null,
                options: optionsComponentFamilies.value
            }
        },
        // {
        //     label: 'Fournisseurs',
        //     name: 'fournisseurs',
        //     trie: false,
        //     type: 'text'
        // },
        // {
        //     label: 'Stocks',
        //     name: 'stocks',
        //     trie: false,
        //     type: 'text'
        // },
        // {
        //     label: 'Besoins enregistrés',
        //     name: 'besoin',
        //     trie: false,
        //     type: 'text'
        // },
        {
            label: 'Etat',
            name: 'state',
            trie: false,
            options: {
                label: value =>
                    optionsEtat.find(option => option.type === value)?.text ?? null,
                options: optionsEtat
            },
            type: 'select'
        }
    ]
    const fetchUser = useUser()
    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || fetchUser.isPurchaseAdmin
    const roleuser = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')
    const itemsTable = computed(() => StoreComponents.componentItems)

    let fInput = {}
    const family = ref('')
    const myBooleanFamily = ref(false)
    const fieldsAttributs = ref([])
    const attributesFiltered = ref([])
    const inputAttributes = ref({})
    let tabInput = []
    const componentListCriteria = useFetchCriteria('component-list-criteria')

    onMounted(async () => {
        await StoreComponents.fetch()
        await fecthOptionsComponentFamilies.fetchOp()
        await storeAttributes.getAttributes()
        await storeUnits.getUnits()
        await storeColors.getListColors()
        console.log(itemsTable.value)
        console.log(optionsComponentFamilies.value)
    })

    async function refreshTable() {
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }

    async function input(formInput) {
        fInput = computed(() => formInput)
        const oldFamily = family.value
        family.value = fInput.value.family
        if (oldFamily === family.value) {
            myBooleanFamily.value = false
        } else {
            myBooleanFamily.value = true
        }
        if (typeof family.value !== 'undefined') {
            inputAttributes.value = {}
            await storeAttributes.getAttributes()
            await storeUnits.getUnits()
            const listUnits = storeUnits.unitsOption
            await storeColors.getListColors()
            const listColors = storeColors.colorsOption

            attributesFiltered.value = storeAttributes.listAttributes.filter(attribute => attribute.families.includes(family.value))
            const newFields = attributesFiltered.value.map(attribute => {
                if (attribute.type === 'color') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listColors.find(option => option.type === value)?.text ?? null,
                            options: listColors
                        },
                        type: 'select'
                    }
                } if (attribute.type === 'measureSelect') {
                    return {
                        name: attribute.name,
                        label: attribute.name,
                        options: {
                            label: value =>
                                listUnits.find(option => option.type === value)?.text ?? null,
                            options: listUnits
                        },
                        type: attribute.type
                    }
                }
                return {
                    name: attribute.name,
                    label: attribute.name,
                    type: attribute.type
                }
            })
            fieldsAttributs.value = newFields
        }
    }

    function inputAttribute(data) {
        inputAttributes.value = data
    }
    async function Componentcreate() {
        const componentInput = {
            family: fInput.value.family,
            manufacturer: fInput.value.manufacturer,
            manufacturerCode: fInput.value.manufacturerCode,
            name: fInput.value.name,
            unit: fInput.value.unit,
            weight: fInput.value.weight
        }
        await StoreComponents.addComponent(componentInput)
        const newComponent = await StoreComponents.component
        tabInput = []
        for (const key in inputAttributes.value.formInput) {
            if (typeof inputAttributes.value.formInput[key] === 'object') {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    measure: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            } else if (key === 'couleur') {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    color: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            } else {
                const attribute = attributesFiltered.value.find(item => item.name === key)
                tabInput.push({
                    attribute: attribute['@id'],
                    value: inputAttributes.value.formInput[key],
                    component: newComponent['@id']
                })
            }
        }
        for (const key in tabInput){
            StoreComponentAttributes.addComponentAttributes(tabInput[key])
        }
    }
    function onComponentShowRequest(item) {
        console.log('onComponentShowRequest', item)
        /* eslint-disable camelcase */
        router.push({name: 'component', params: {id_component: item.id}})
    }

    async function cancelSearch() {
        componentListCriteria.resetAllFilter()
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function deleted(id){
        await StoreComponents.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        componentListCriteria.gotoPage(parseFloat(nPage))
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function search(inputValues) {
        componentListCriteria.resetAllFilter()
        if (inputValues.code) componentListCriteria.addFilter('code', inputValues.code)
        if (inputValues.index) componentListCriteria.addFilter('index', inputValues.index)
        if (inputValues.name) componentListCriteria.addFilter('name', inputValues.name)
        if (inputValues.family) componentListCriteria.addFilter('family', inputValues.family)
        if (inputValues.state) componentListCriteria.addFilter('embState.state[]', inputValues.state)
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        componentListCriteria.addSort(payload.name, payload.direction)
        await StoreComponents.fetch(componentListCriteria.getFetchCriteria)
    }
</script>

<template>
    <div class="row">
        <div class="row">
            <div class="col">
                <h1>
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    Liste des composants
                    <span v-if="isPurchaseWriterOrAdmin" class="btn-float-right">
                        <AppBtn
                            variant="success"
                            label="Créer"
                            data-bs-toggle="modal"
                            :data-bs-target="target">
                            <Fa icon="plus"/>
                            Créer
                        </AppBtn>
                    </span>
                </h1>
            </div>
        </div>
        <AppModal :id="modalId" class="four" :title="title" size="xl">
            <AppSuspense>
                <AppComponentCreate :fields-attributs="fieldsAttributs" :my-boolean-family="myBooleanFamily" @update:model-value="input" @data-attribute="inputAttribute"/>
            </AppSuspense>
            <template #buttons>
                <AppBtn
                    variant="success"
                    label="Créer"
                    data-bs-toggle="modal"
                    :data-bs-target="target"
                    @click="Componentcreate">
                    Créer
                </AppBtn>
            </template>
        </AppModal>

        <div class="col">
            <Suspense>
                <AppCardableTable
                    :current-page="StoreComponents.currentPage"
                    :fields="fields"
                    :first-page="StoreComponents.firstPage"
                    :items="itemsTable"
                    :last-page="StoreComponents.lastPage"
                    :next-page="StoreComponents.nextPage"
                    :pag="StoreComponents.pagination"
                    :previous-page="StoreComponents.previousPage"
                    :user="roleuser"
                    form="formComponentCardableTable"
                    @cancel-search="cancelSearch"
                    @deleted="deleted"
                    @get-page="getPage"
                    @search="search"
                    @trier-alphabet="trierAlphabet"
                    @update="onComponentShowRequest"/>
            </Suspense>
        </div>
    </div>
</template>
