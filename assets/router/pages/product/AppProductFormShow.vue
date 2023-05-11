<script setup>
    import MyTree from '../../../components/MyTree.vue'
    import {computed} from 'vue'
    import generateProduct from '../../../stores/product/product'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import useOptions from '../../../stores/option/options'
    import {useProductAttachmentStore} from '../../../stores/product/productAttachement'
    import {useProductStore} from '../../../stores/product/products'

    const fecthOptions = useOptions('units')
    const fetchProductStore = useProductStore()
    const fetchProductAttachmentStore = useProductAttachmentStore()
    const fetchIncotermStore = useIncotermStore()

    await fetchProductStore.fetch()
    await fetchProductStore.fetchProductFamily()
    await fecthOptions.fetchOp()
    await fetchProductAttachmentStore.fetch()
    await fetchIncotermStore.fetch()

    const productAttachment = computed(() =>
        fetchProductAttachmentStore.productAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(),
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: productAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${productAttachment.value.length})`

        }
        return data
    })
    const optionsIncoterm = computed(() =>
        fetchIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsUnit = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.value
            const optionList = {text, value}
            return optionList
        }))
    const optionsProductFamily = computed(() =>
        fetchProductStore.productsFamily.map(op => {
            const text = op.fullName
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const Géneralitésfields = [
        {
            label: 'Famille',
            name: 'family',
            options: {
                label: value =>
                    optionsProductFamily.value.find(option => option.type === value)
                        ?.text ?? null,
                options: optionsProductFamily.value
            },
            type: 'select'
        },
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsUnit.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnit.value
            },
            type: 'select'
        },
        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Gestion du cuivre', name: 'managedCopper', type: 'boolean'}
    ]
    const Projectfields = [
        {label: 'Date Fin', name: 'getEndOfLife', type: 'date'},
        {label: 'maxProto', name: 'maxProto', type: 'measure'},
        {label: 'Prix', name: 'price', type: 'measure'},
        {label: 'Prix Cuivre', name: 'priceWithoutCopper', type: 'measure'},
        {
            label: 'Prix Transfer Fournisseur',
            name: 'transfertPriceSupplies',
            type: 'measure'
        },
        {label: 'Prix Transfer Work', name: 'transfertPriceWork', type: 'measure'}
    ]
    const Productionfields = [
        {label: 'Duration Auto', name: 'autoDuration', type: 'measure'},
        {label: 'Duration Manual', name: 'manualDuration', type: 'measure'},
        {label: 'Production Min', name: 'minProd', type: 'measure'},
        {label: 'Production Delay', name: 'productionDelay', type: 'measure'},
        {label: 'Foce Volume', name: 'forecastVolume', type: 'measure'},
        {label: 'Packaging', name: 'packaging', type: 'measure'},
        {label: 'Packaging Kind', name: 'packagingKind', type: 'text'}
    ]
    const Logistiquefields = [
        {label: 'Code', name: 'customsCode', type: 'text'},
        {
            label: 'Incoterms',
            name: 'incoterms',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {label: 'Stock Min', name: 'minStock', type: 'measure'},
        {label: 'Delivery Min', name: 'minDelivery', type: 'measure'},
        {label: 'weight', name: 'weight', type: 'measure'}

    ]
    const Adminfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Index', name: 'index', type: 'text'},
        {label: 'Index interne', name: 'internalIndex', type: 'text'},
        {label: 'Kind', name: 'kind', type: 'text'}
    ]
    const Fichiersfields = [{label: 'Fichier', name: 'file', type: 'file'}]

    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        console.log('value', value)

        const data = {
            family: formData.get('family'),
            notes: formData.get('notes')
        }

        const item = generateProduct(value)
        await item.updateMain(data)
        await fetchProductStore.fetch()
    }
    async function updateAdmin(value) {
        const form = document.getElementById('addAdmin')
        const formData = new FormData(form)
        console.log('value', value)

        const data = {

            code: formData.get('code'),
            index: formData.get('index'),
            internalIndex: formData.get('internalIndex'),
            kind: formData.get('kind'),
            name: formData.get('name')

        }

        const item = generateProduct(value)
        await item.updateAdmin(data)
        await fetchProductStore.fetch()
    }
    async function updateProject(value) {
        const form = document.getElementById('addProject')
        const formData = new FormData(form)
        console.log('value', value)

        const data = {
            endOfLife: formData.get('getEndOfLife'),
            maxProto: {
                code: formData.get('maxProto-code'),
                value: JSON.parse(formData.get('maxProto-value'))
            }
        }

        const item = generateProduct(value)
        await item.updateProject(data)
        await fetchProductStore.fetch()
    }
    async function updateProduction(value) {
        const form = document.getElementById('addProduction')
        const formData = new FormData(form)
        console.log('value', value)

        const data = {

            autoDuration: {
                code: formData.get('autoDuration-code'),
                value: JSON.parse(formData.get('autoDuration-value'))
            },
            forecastVolume: {
                code: formData.get('forecastVolume-code'),
                value: JSON.parse(formData.get('forecastVolume-value'))
            },
            manualDuration: {
                code: formData.get('manualDuration-code'),
                value: JSON.parse(formData.get('manualDuration-value'))
            },
            minProd: {
                code: formData.get('minProd-code'),
                value: JSON.parse(formData.get('minProd-value'))
            },
            packaging: {
                code: formData.get('packaging-code'),
                value: JSON.parse(formData.get('packaging-value'))
            },
            packagingKind: formData.get('packagingKind'),
            productionDelay: {
                code: formData.get('productionDelay-code'),
                value: JSON.parse(formData.get('productionDelay-value'))
            }
        }

        const item = generateProduct(value)
        await item.updateProduction(data)
        await fetchProductStore.fetch()
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)
        console.log('value', value)

        const data = {
            customsCode: formData.get('customsCode'),
            incoterms: formData.get('incoterms'),
            minDelivery: {
                code: formData.get('minDelivery-code'),
                value: JSON.parse(formData.get('minDelivery-value'))
            },
            minStock: {
                code: formData.get('minStock-code'),
                value: JSON.parse(formData.get('minStock-value'))
            },

            weight: {
                code: formData.get('weight-code'),
                value: JSON.parse(formData.get('weight-value'))
            }

        }
        const item = generateProduct(value)
        await item.updateLogistique(data)
        await fetchProductStore.fetch()
    }
    function updateFichiers(value) {
        const productsId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: 'doc',
            file: formData.get('file'),
            product: `/api/products/${productsId}`
        }

        fetchProductAttachmentStore.ajout(data)
        productAttachment.value = computed(() =>
            fetchProductAttachmentStore.productAttachment.map(attachment => ({
                icon: 'file-contract',
                id: attachment['@id'],
                label: attachment.url.split('/').pop(),
                url: attachment.url
            })))
        treeData.value = {
            children: productAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${productAttachment.value.length})`
        }
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addGeneralites"
                :fields="Géneralitésfields"
                :component-attribute="fetchProductStore.products"
                @update="updateGeneral(fetchProductStore.products)"/>
        </AppTab>
        <AppTab
            id="gui-start-project"
            title="Project"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addProject"
                :fields="Projectfields"
                :component-attribute="fetchProductStore.products"
                @update="updateProject(fetchProductStore.products)"/>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addProduction"
                :fields="Productionfields"
                :component-attribute="fetchProductStore.products"
                @update="updateProduction(fetchProductStore.products)"/>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppCardShow id="addQualite"/>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addLogistique"
                :fields="Logistiquefields"
                :component-attribute="fetchProductStore.products"
                @update="updateLogistique(fetchProductStore.products)"/>
        </AppTab>
        <AppTab
            id="gui-start-purchase-admin"
            title="Admin"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addAdmin"
                :fields="Adminfields"
                :component-attribute="fetchProductStore.products"
                @update="updateAdmin(fetchProductStore.products)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fetchProductStore.products)"/>
            <MyTree :node="treeData"/>
        </AppTab>
        <AppTab
            id="gui-start-Prix"
            title="Prix"
            icon="circle-dollar-to-slot"
            tabs="gui-start">
            <AppCardShow id="addPrice"/>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresses\Contacts"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow id="addAdresses"/>
        </AppTab>
    </AppTabs>
</template>
