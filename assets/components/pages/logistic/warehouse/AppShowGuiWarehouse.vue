<script setup>
    import {computed, ref, onBeforeMount, onBeforeUpdate} from 'vue'
    import {useRouter, useRoute} from 'vue-router'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {
        useWarehouseShowStore as warehouseStore
    } from '../../../../stores/logistic/warehouses/warehouseShow'
    import useOptions from '../../../../stores/option/options'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppWarehouseListShow from './AppWarehouseListShow.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const router = useRouter()
    const store = warehouseStore()
    const localData = ref({})
    let key = 0
    const fecthCompanyOptions = useOptions('companies')

    const idWarehouse = Number(route.params.id_warehouse)
    store.setCurrentId(idWarehouse)
    function goBack() {
        router.push({name: 'warehouse-list'})
    }
    function initializeLocalData() {
        localData.value = {
            name: store.items.name,
            company: store.items.company,
            families: store.items.families,
            destination: store.items.destination
        }
    }
    function hasCamion(){
        const generalFamlies = store.getFamilies
        for (const el of generalFamlies){
            if (el === 'camion'){
                return true
            }
        }
        return false
    }
    //region chargement des compagnies
    async function loadData() {
        //console.log('loadData', fecthCompanyOptions.hasOptions)
        if (fecthCompanyOptions.hasOptions === false){
            await fecthCompanyOptions.fetchOp()
        }
        console.log('fecthCompanyOptions.options', fecthCompanyOptions)
        const probl = await store.fetch()
        if (probl === true) route.push({name: 'warehouse-list'}) //{props.item.id}\`
        //console.log('store', store)
        if (hasCamion() === true) Generalitesfields.value.push(
            {
                label: 'Destination',
                name: 'destination',
                options: {
                    label: value =>
                        optionsDestination.value.find(option => option.type === value)?.text
                        ?? null,
                    options: optionsDestination.value
                },
                type: 'select'
            }
        )
        initializeLocalData()
        // console.log('', Generalitesfields.value)
        key++
    }
    onBeforeMount(() => {
        // console.log('onBeforeMount')
        loadData()
    })
    onBeforeUpdate(() => {
        // console.log('onBeforeUpdate')
        loadData()
    })
    const optionsCompany = computed(() => fecthCompanyOptions.options.map(op => ({text: op.text, value: op['@id']})))
    //endregion
    const Generalitesfields = ref([])
    Generalitesfields.value = [
        {label: 'Name', name: 'name', type: 'text'},
        {
            label: 'Company',
            name: 'company',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'select'
        },
        {
            label: 'Famille ',
            name: 'families',
            options: {
                options: [
                    {
                        text: 'Prison',
                        value: 'prison'
                    },
                    {
                        text: 'Production',
                        value: 'production'
                    },
                    {
                        text: 'Réception',
                        value: 'réception'
                    },
                    {
                        text: 'Magasin pièces finies',
                        value: 'magasin pièces finies'
                    },
                    {
                        text: 'Expédition',
                        value: 'expédition'
                    },
                    {
                        text: 'Magasin matières premières',
                        value: 'magasin matières premières'
                    },
                    {
                        text: 'Camion',
                        value: 'camion'
                    }
                ]
            },
            type: 'multiselect'
        }
    ]
    function localDataChange(value) {
        if (value.families.includes('camion')) {
            //on doit s'assurer que les fields ont bien destination, sinon on ajoute
            if (typeof Generalitesfields.value.find(item => item.name === 'destination') === 'undefined') {
                Generalitesfields.value.push(
                    {
                        label: 'Destination',
                        name: 'destination',
                        options: {
                            label: valeur =>
                                optionsDestination.value.find(option => option.type === valeur)?.text
                                ?? null,
                            options: optionsDestination.value
                        },
                        type: 'select'
                    }
                )
            }
        } else if (typeof Generalitesfields.value.find(item => item.name === 'destination') !== 'undefined') {
            Generalitesfields.value.pop()
            localData.value.destination = null
        }
    }
    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        store.setName(formData.get('name'))
        store.setCompany(formData.get('company'))
        const strFamilies = formData.get('families')
        store.setFamilies(strFamilies.split(','))
        store.setDestination(formData.get('destination'))

        await store.update()
        await store.fetch()
    }
    async function cancel() {
        initializeLocalData()
        key++
    }
    // initializeLocalData()
    //region chargement des options de destination
    // const fecthDestinationOptions = fecthCompanyOptions
    // const opEmpty = {
    //     text: ' ',
    //     '@id': null
    // }
    // fecthDestinationOptions.options.push(opEmpty)
    // const optionsDestination = computed(() =>
    //     optionsCompany.options.map(op => {
    //         const text = op.text
    //         const value = op['@id']
    //         const optionList = {text, value}
    //         return optionList
    //     }))
    const optionsDestination = computed(() => [
        {text: '', value: null},
        ...optionsCompany.value
    ])
    //endregion
</script>

<template>
    <AppShowGuiGen>
        <template #gui-left>
            <div class="row">
                <div class="bg-white">
                    <h1>
                        <button class="text-dark" @click="goBack">
                            <Fa :icon="icon"/>
                        </button>
                        {{ title }} - {{ store.getNameWarehouse }}
                    </h1>
                </div>
            </div>
            <div class="row">
                <AppCardShow
                    id="addGeneralites"
                    :key="key"
                    :fields="Generalitesfields"
                    :component-attribute="localData"
                    @cancel="cancel"
                    @update="updateGeneral"
                    @update:model-value="localDataChange"/>
            </div>
        </template>
        <template #gui-right>
            <div>RIGHT</div>
            <!-- {{ route.params.id_employee }} -->
        </template>
        <template #gui-bottom>
            <AppSuspense><AppWarehouseListShow/></AppSuspense>
        </template>
    </AppShowGuiGen>
</template>
