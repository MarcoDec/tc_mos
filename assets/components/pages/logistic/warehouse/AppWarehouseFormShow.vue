<script setup>
    import {computed, ref} from 'vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {
        useWarehouseShowStore as warehouseStore
    } from '../../../../stores/logistic/warehouses/warehouseShow'
    import {useRoute} from 'vue-router'
    import useOptions from '../../../../stores/option/options'
    import {useWarehouseAttachmentStore} from '../../../../stores/logistic/warehouses/warehouseAttachements'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'

    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    const store = warehouseStore()
    store.setCurrentId(warehouseId)
    const probl = await store.fetch()
    if (probl) maRoute.push({name: 'warehouse-list'}) //{props.item.id}\`
    const localData = ref({})
    function initializeLocalData() {
        localData.value = {
            name: store.items.name,
            company: store.items.company,
            families: store.items.families,
            destination: store.items.destination
        }
    }
    initializeLocalData()
    let key = 0
    //region chargement des pièces jointes
    const warehouseAttachmentStore = useWarehouseAttachmentStore()
    warehouseAttachmentStore.fetchByElement(warehouseId)
    //endregion
    //region chargement des compagnies
    const fecthCompanyOptions = useOptions('companies')
    if (fecthCompanyOptions.hasOptions === false){
        await fecthCompanyOptions.fetchOp()
    }
    const optionsCompany = computed(() => fecthCompanyOptions.options.map(op => ({text: op.text, value: op['@id']})))
    //endregion
    //region chargement des options de destination
    const fecthDestinationOptions = fecthCompanyOptions
    const opEmpty = {
        text: ' ',
        '@id': null
    }
    fecthDestinationOptions.options.push(opEmpty)
    const optionsDestination = computed(() =>
        fecthDestinationOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    //endregion
    function hasCamion(){
        const generalFamlies = store.getFamilies
        for (const el of generalFamlies){
            if (el === 'camion'){
                return true
            }
        }
        return false
    }
    const Generalitesfields = [
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
    if (hasCamion() === true) Generalitesfields.push(
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

    function localDataChange(value) {
        if (value.families.includes('camion')) {
            //on doit s'assurer que les fields ont bien destination, sinon on ajoute
            if (typeof Generalitesfields.find(item => item.name === 'destination') === 'undefined') {
                Generalitesfields.push(
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
        } else if (typeof Generalitesfields.find(item => item.name === 'destination') !== 'undefined') {
            Generalitesfields.pop()
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
</script>

<template>
    <div style="min-height: 45vh; height: 45vh; background-color: rgba(255, 255, 255, 0.8)">
        <AppSuspense>
            <AppCardShow
                id="addGeneralites"
                :key="key"
                :fields="Generalitesfields"
                :component-attribute="localData"
                @cancel="cancel"
                @update="updateGeneral"
                @update:model-value="localDataChange"/>
        </AppSuspense>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
    #gui-start-main {
        padding-bottom: 150px;
    }
</style>
