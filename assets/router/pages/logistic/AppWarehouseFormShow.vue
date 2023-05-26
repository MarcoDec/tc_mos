<script setup>
    import {computed, ref} from 'vue'
    import {useWarehouseShowStore as warehouseStore} from '../../../stores/logistic/warehouses/warehouseShow.js'
    import {useSocietyListStore} from '../../../stores/direction/societyList.js'
    import {useRoute} from 'vue-router'
    //import {useComponentListStore} from '../../../stores/component/components'
    //import {useComponentShowStore} from '../../../stores/component/componentAttributesList'
    import useOptions from '../../../stores/option/options'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    const fecthCompanyOptions = useOptions('companies')
    if (fecthCompanyOptions.hasOptions === false){
        await fecthCompanyOptions.fetchOp()
    }

    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse

    const store = warehouseStore()
    const storeCompanyAll = useSocietyListStore()
    storeCompanyAll.fetch()

    const optionsCompany = computed(() =>
        fecthCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))

    function hasCamion(){
        const generalFamlies = store.getFamilies
        for (const el of generalFamlies){
            if (el === 'camion'){
                return true
            }
        }
        return false
    }

    let Generalitesfields = []
    const probl = await store.fetch()
    //const storedId = sessionStorage.getItem('warehouseID')
    if (warehouseId === null || probl === true){
        console.log('aie')
        maRoute.push('/warehouse-list') //{props.item.id}\`
    } else {
        // store.setCurrentId(props.)
        Generalitesfields = [
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
            }
        ]

        if (hasCamion() === true){
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

            Generalitesfields.push(
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
        }

        Generalitesfields.push(
            {
                label: 'Famille ',
                name: 'families',
                options: [
                    {
                        disabled: false,
                        label: 'Prison',
                        value: 'prison'
                    },
                    {
                        disabled: false,
                        label: 'Production',
                        value: 'production'
                    },
                    {
                        disabled: false,
                        label: 'Réception',
                        value: 'réception'
                    },
                    {
                        disabled: false,
                        label: 'Magasin pièces finies',
                        value: 'magasin pièces finies'
                    },
                    {
                        disabled: false,
                        label: 'Expédition',
                        value: 'expédition'
                    },
                    {
                        disabled: false,
                        label: 'Magasin matières premières',
                        value: 'magasin matières premières'
                    },
                    {
                        disabled: false,
                        label: 'Camion',
                        value: 'camion'
                    }
                ],
                optionsList: {
                    camion: 'camion',
                    expédition: 'expédition',
                    'magasin matières premières': 'magasin matières premières',
                    'magasin pièces finies': 'magasin pièces finies',
                    prison: 'prison',
                    production: 'production',
                    réception: 'réception'
                },
                type: 'multiselect'
            }
        )
    }

    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        store.setName(formData.get('name'))
        store.setCompany(formData.get('company'))
        store.setDestination(formData.get('destination'))

        await store.update()
        await store.fetch()
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralité"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addGeneralites"
                :fields="Generalitesfields"
                :component-attribute="store.items"
                @update="updateGeneral"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichier" icon="folder" tabs="gui-start">
            <div class="container-fluid">
                <AppRow>
                    <AppCol class="col-1">
                        <AppBtnJS variant="primary">
                            CSV
                        </AppBtnJS>
                    </AppCol>
                    <AppCol>
                        <div class="input-group mb-3">
                            <input id="inputGroupFile02" type="file" class="form-control"/>
                            <label class="input-group-text" for="inputGroupFile02">Rechercher</label>
                            <AppBtnJS variant="success">
                                Upload
                            </AppBtnJS>
                        </div>
                        <p> Format supporté : .csv ( séparé par des points virgules ) </p>
                    </AppCol>
                </AppRow>
            </div>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
