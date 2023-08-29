<script setup>
    import {computed} from 'vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {
        useWarehouseShowStore as warehouseStore
    } from '../../../../stores/logistic/warehouses/warehouseShow'
    import {useSocietyListStore} from '../../../../stores/management/societyList'
    import {useRoute} from 'vue-router'
    import useOptions from '../../../../stores/option/options'
    import {useWarehouseAttachmentStore} from '../../../../stores/logistic/warehouses/warehouseAttachements'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'

    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    const warehouseAttachmentStore = useWarehouseAttachmentStore()
    warehouseAttachmentStore.fetchByElement(warehouseId)
    const fecthCompanyOptions = useOptions('companies')
    if (fecthCompanyOptions.hasOptions === false){
        await fecthCompanyOptions.fetchOp()
    }


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
    store.setCurrentId(warehouseId)
    const probl = await store.fetch()
    if (probl){
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
        <AppSuspense>
            <AppTab
                id="gui-start-main"
                active
                title="Généralité"
                icon="pencil"
                tabs="gui-start">
                <AppSuspense>
                    <AppCardShow
                        id="addGeneralites"
                        :fields="Generalitesfields"
                        :component-attribute="store.items"
                        @update="updateGeneral"/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-files" title="Fichier" icon="folder" tabs="gui-start">
                <AppSuspense>
                    <AppTabFichiers
                        attachment-element-label="warehouse"
                        :element-api-url="`/api/warehouses/${warehouseId}`"
                        :element-attachment-store="warehouseAttachmentStore"
                        :element-id="warehouseId"
                        element-parameter-name="WAREHOUSE_ATTACHMENT_CATEGORIES"
                        :element-store="warehouseStore"/>
                </AppSuspense>
            </AppTab>
        </AppSuspense>
    </AppTabs>
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
