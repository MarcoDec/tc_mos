<script setup>
    import {computed} from 'vue'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import useOptions from '../../../stores/option/options'
    import {useSocietyStore} from '../../../stores/societies/societies'

    import {useSupplierAttachmentStore} from '../../../stores/supplier/supplierAttachement'
    import {useSuppliersStore} from '../../../stores/supplier/suppliers'

    const fecthCurrencyOptions = useOptions('currencies')
    const fecthOptions = useOptions('countries')
    const fecthSuppliersStore = useSuppliersStore()
    const fecthIncotermStore = useIncotermStore()
    const fecthSocietyStore = useSocietyStore()
    const fecthSupplierAttachmentStore = useSupplierAttachmentStore()
    await fecthSuppliersStore.fetch()
    await fecthSuppliersStore.fetchVatMessage()
    await fecthIncotermStore.fetch()
    await fecthSocietyStore.fetch()
    await fecthCurrencyOptions.fetch()
    await fecthOptions.fetch()

    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    const optionsCurrency = computed(() =>
        fecthCurrencyOptions.options.map(op => {
            const text = op.text
            const value = op.value
            const optionList = {text, value}
            return optionList
        }))
    const optionsVat = computed(() =>
        fecthSuppliersStore.vatMessage.map(op => {
            const text = op.name
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))

    const societyId = Number(fecthSuppliersStore.suppliers.society.match(/\d+/))
    await fecthSocietyStore.fetchById(societyId)
    const dataSuppliers = computed(() =>
        Object.assign(fecthSuppliersStore.suppliers, fecthSocietyStore.item))
    const managed = computed(() => {
        const data = {managedCopper: dataSuppliers.value.copper.managed}
        return data
    })
    const list = computed(() =>
        Object.assign(fecthSocietyStore.item, managed.value))

    const listSuppliers = computed(() =>
        Object.assign(dataSuppliers.value, list.value))

    const optionsIncoterm = computed(() =>
        fecthIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm.id
            const optionList = {text, value}
            return optionList
        }))

    const Géneralitésfields = [
        {
            label: 'Gestion en production',
            name: 'managedProduction',
            type: 'boolean'
        },
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
    const Qualitéfields = [
        {label: 'Gestion de la qualité', name: 'managedQuality', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'confidenceCriteria', type: 'rating'},
        {label: 'Taux de PPM', name: 'ppmRate', type: 'number'}
    ]
    const Achatfields = [
        {label: 'Minimum de commande', name: 'orderMin', type: 'measure'},
        {
            label: 'Incoterm',
            name: 'incoterm',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {label: 'Gestion du cuivre', name: 'managedCopper', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'confidenceCriteria', type: 'rating'},
        {label: 'Commande ouverte', name: 'CommandeOuverte', type: 'boolean'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'}
    ]
    const Comptabilitéfields = [
        {
            label: 'Montant minimum de facture',
            name: 'invoiceMin',
            type: 'measure'
        },
        {
            label: 'Devise',
            name: 'currency',
            options: {
                label: value =>
                    optionsCurrency.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCurrency.value
            },
            type: 'select'
        },
        {label: 'Compte de comptabilité', name: 'CompteComptabilité', type: 'text'},
        {label: 'TVA', name: 'vat', type: 'text'},
        {
            label: 'Forcer la TVA',
            name: 'forceVat',
            type: 'text'
        },
        {
            label: 'Message TVA',
            name: 'MessageTVA',
            options: {
                label: value =>
                    optionsVat.value.find(option => option.type === value)?.text ?? null,
                options: optionsVat.value
            },
            type: 'select'
        }
    ]
    const Fichiersfields = [{label: 'Fichier', name: 'file', type: 'file'}]
    const Adressefields = [
        {label: 'Email', name: 'getEmail', type: 'text'},
        {label: 'Adresse', name: 'getAddress', type: 'text'},
        {label: 'Complément d\'adresse', name: 'getAddress2', type: 'text'},
        {label: 'Code postal', name: 'getPostal', type: 'text'},
        {label: 'Ville', name: 'getCity', type: 'text'},
        {
            label: 'Pays',
            name: 'getCountry',
            options: {
                label: value =>
                    optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCountries.value
            },
            type: 'select'
        },
        {label: 'Fax', name: 'getPhone', type: 'text'}
    ]

    async function update(value) {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {
            confidenceCriteria: formData.get('confidenceCriteria'),
            managedQuality: formData.get('managedQuality'),
            ppmRate: formData.get('ppmRate')
        }
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addAchatLogistique')
        const formData = new FormData(form)
        const data = {
            confidenceCriteria: formData.get('confidenceCriteria'),
            copper: {
                index: {
                    code: 'EUR',
                    value: 1
                },
                //last: "2023-04-13T09:08:53.175Z",
                managed: formData.get('managedCopper')
                //next: "2023-04-13T09:08:53.175Z",
                //type: "mensuel",
            }

        }
    }
    function updateFichiers(value) {
        const suppliersId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: 'doc',
            file: formData.get('file'),
            supplier: `/api/suppliers/${suppliersId}`
        }

        fecthSupplierAttachmentStore.ajout(data)
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
                :component-attribute="fecthSuppliersStore.suppliers"
                @update="updateFichiers(fecthSuppliersStore.suppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fecthSuppliersStore.suppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppCardShow
                id="addQualite"
                :fields="Qualitéfields"
                :component-attribute="listSuppliers"
                @update="update(listSuppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Achat/Logistique"
            icon="bag-shopping"
            tabs="gui-start">
            <AppCardShow
                id="addAchatLogistique"
                :fields="Achatfields"
                :component-attribute="listSuppliers"
                @update="updateLogistique(listSuppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-accounting"
            title="Comptabilité"
            icon="industry"
            tabs="gui-start">
            <AppCardShow
                id="addComptabilite"
                :fields="Comptabilitéfields"
                :component-attribute="listSuppliers"
                @update="updateLogistique(listSuppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresses"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow
                id="addAdresses"
                :fields="Adressefields"
                :component-attribute="fecthSuppliersStore.suppliers"
                @update="updateFichiers(fecthSuppliersStore.suppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow id="addContacts"/>
        </AppTab>
    </AppTabs>
</template>
