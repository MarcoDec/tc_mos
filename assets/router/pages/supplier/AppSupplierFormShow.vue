<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../components/MyTree.vue'
    import generateSocieties from '../../../stores/societies/societie'
    import generateSupplier from '../../../stores/supplier/supplier'
    import generateSupplierContact from '../../../stores/supplier/supplierContact'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import useOptions from '../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../stores/societies/societies'
    import {useSupplierAttachmentStore} from '../../../stores/supplier/supplierAttachement'
    import {useSupplierContactsStore} from '../../../stores/supplier/supplierContacts'
    import {useSuppliersStore} from '../../../stores/supplier/suppliers'

    const route = useRoute()
    const idSupplier = route.params.id_supplier

    const emit = defineEmits([
        'update',
        'update:modelValue',
        'rating',
        'cancelSearch'
    ])
    const isError = ref(false)
    const isError2 = ref(false)
    const isShow = ref(false)
    const violations = ref([])
    const violations2 = ref([])
    const fetchCurrencyOptions = useOptions('currencies')
    const fetchCompanyOptions = useOptions('companies')
    const fetchOptions = useOptions('countries')
    const fetchSuppliersStore = useSuppliersStore()
    const fetchIncotermStore = useIncotermStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchSupplierAttachmentStore = useSupplierAttachmentStore()
    const fetchSupplierContactsStore = useSupplierContactsStore()

    await fetchSuppliersStore.fetchOne(idSupplier)
    await fetchSuppliersStore.fetchVatMessage()
    await fetchIncotermStore.fetch()
    await fetchSocietyStore.fetch()
    await fetchCurrencyOptions.fetchOp()
    await fetchSupplierAttachmentStore.fetch()
    await fetchOptions.fetchOp()
    await fetchCompanyOptions.fetchOp()
    const supplierAttachment = computed(() =>
        fetchSupplierAttachmentStore.supplierAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(),
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: supplierAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${supplierAttachment.value.length})`
        }
        return data
    })
    const optionsVatMessageForce = [
        {
            text: 'TVA par défaut selon le pays du client',
            value: 'TVA par défaut selon le pays du client'
        },
        {text: 'Force AVEC TVA', value: 'Force AVEC TVA'},
        {text: 'Force SANS TVA', value: 'Force SANS TVA'}
    ]
    const optionsCompany = computed(() =>
        fetchCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsCountries = computed(() =>
        fetchOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    const optionsCurrency = computed(() =>
        fetchCurrencyOptions.options.map(op => {
            const text = op.text
            const value = op.value
            const optionList = {text, value}
            return optionList
        }))
    const optionsVat = computed(() =>
        fetchSuppliersStore.vatMessage.map(op => {
            const text = op.name
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))

    const societyId = Number(fetchSuppliersStore.supplier.society.match(/\d+/))
    const supplierId = Number(fetchSuppliersStore.supplier.id)
    await fetchSocietyStore.fetchById(societyId)
    await fetchSupplierContactsStore.fetchBySociety(societyId)

    const itemsTable = computed(() =>
        fetchSupplierContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))

    // const dataSuppliers = computed(() =>
    //     Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.item))
    const managed = computed(() => {
        const data = {managedCopper: fetchSocietyStore.society.copper.managed}
        return data
    })
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchSocietyStore.society.invoiceMin.code = 'EUR'

    const list = computed(() => ({...fetchSocietyStore.society, ...managed.value}))

    const listSuppliers = computed(() => ({
        ...fetchSuppliersStore.supplier,
        ...list.value,
        ...fetchSocietyStore.vatMessageValue,
        ...fetchSocietyStore.incotermsValue
    }))
    const optionsIncoterm = computed(() =>
        fetchIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            const optionList = {text, value}
            return optionList
        }))

    const fieldsSupp = [
        {
            label: 'Nom ',
            name: 'name',
            type: 'text'
        },
        {
            label: 'Prenom ',
            name: 'surname',

            type: 'text'
        },
        {
            label: 'Mobile ',
            name: 'mobile',

            type: 'text'
        },
        {
            label: 'Email ',
            name: 'email',

            type: 'text'
        },
        {
            label: 'Adresse',
            name: 'address',
            type: 'text'
        },
        {
            label: 'Complément d\'adresse ',
            name: 'address2',
            type: 'text'
        },
        {
            label: 'Pays',
            name: 'country',
            options: {
                label: value =>
                    optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCountries.value
            },
            type: 'select'
        },
        {
            label: 'Ville ',
            name: 'city',
            type: 'text'
        },
        {
            label: 'Code Postal ',
            name: 'zipCode',
            type: 'text'
        }
    ]

    const Géneralitésfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {
            label: 'Compagnies',
            name: 'administeredBy',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'select'
        },
        {label: 'Langue', name: 'language', type: 'text'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
    const Qualitéfields = [
        {label: 'Gestion de la qualité', name: 'managedQuality', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'confidenceCriteria', type: 'rating'},
        {label: 'Taux de PPM', name: 'ppmRate', type: 'number'}
    ]
    const Achatfields = [
        {
            label: 'Minimum de commande',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'orderMin',
            type: 'measure'
        },
        {
            label: 'Incoterm',
            name: 'incotermsValue',
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
        {label: 'Commande ouverte', name: 'openOrdersEnabled', type: 'boolean'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'}
    ]
    const Comptabilitéfields = [
        {
            label: 'Montant minimum de facture',
            measure: {code: 'Devise', value: 'valeur'},
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
        {label: 'Compte de comptabilité', name: 'accountingAccount', type: 'text'},
        {label: 'TVA', name: 'vat', type: 'text'},
        {
            label: 'Forcer la TVA',
            name: 'forceVat',
            options: {
                label: value =>
                    optionsVatMessageForce.find(option => option.type === value)?.text
                    ?? null,
                options: optionsVatMessageForce
            },
            type: 'select'
        },
        {
            label: 'Message TVA',
            name: 'vatMessageValue',
            options: {
                label: value =>
                    optionsVat.value.find(option => option.type === value)?.text ?? null,
                options: optionsVat.value
            },
            type: 'select'
        }
    ]
    const Fichiersfields = [
        {label: 'Categorie', name: 'category', type: 'text'},
        {label: 'Fichier', name: 'file', type: 'file'}
    ]
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
    const val = ref(Number(fetchSuppliersStore.supplier.confidenceCriteria))
    async function input(value) {
        val.value = value.confidenceCriteria
        emit('update:modelValue', val.value)
        const data = {
            confidenceCriteria: val.value
        }
        const item = generateSupplier(value)
        await item.updateQuality(data)
        await fetchSocietyStore.fetch()
    }
    async function update(value) {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)

        const data = {
            managedQuality: JSON.parse(formData.get('managedQuality'))
        }
        const dataSociety = {
            ppmRate: JSON.parse(formData.get('ppmRate'))
        }

        const item = generateSupplier(value)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(dataSociety)
        await item.updateQuality(data)
        await fetchSocietyStore.fetch()
    }
    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)

        const data = {
            //managedProduction: JSON.parse(formData.get("managedProduction")),
            //administeredBy:  formData.get("administeredBy"),
            //administeredBy: ["/api/companies/1"],
            language: formData.get('language'),
            notes: formData.get('notes')
        }
        const dataAdmin = {
            administeredBy: [formData.get('administeredBy')],
            name: formData.get('name')
        }
        const item = generateSupplier(value)
        await item.updateMain(data)
        await item.updateAdmin(dataAdmin)
        await fetchSuppliersStore.fetchOne(idSupplier)
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addAchatLogistique')
        const formData = new FormData(form)

        const dataSociety = {
            ar: JSON.parse(formData.get('ar')),
            copper: {
                managed: JSON.parse(formData.get('managedCopper'))
            },
            incoterms: formData.get('incotermsValue'),
            orderMin: {
                code: 'EUR',
                value: JSON.parse(formData.get('orderMin-value'))
            }
        }
        const data = {
            openOrdersEnabled: JSON.parse(formData.get('openOrdersEnabled'))
        }
        // const itemSoc = generateSocieties(value);
        // await itemSoc.update(dataSociety);
        await fetchSocietyStore.update(dataSociety, societyId)
        await fetchSocietyStore.fetchById(societyId)
        const item = generateSupplier(value)
        await item.updateLog(data)
        await fetchSocietyStore.fetch()

        list.value = computed(() => ({
            ...fetchSocietyStore.society,
            ...managed.value
        }))

        listSuppliers.value = computed(() => ({
            ...fetchSuppliersStore.supplier,
            ...list.value,
            ...fetchSocietyStore.incotermsValue
        }))
    }

    async function updateAddress(value) {
        const form = document.getElementById('addAdresses')
        const formData = new FormData(form)

        const data = {
            address: {
                address: formData.get('getAddress'),
                address2: formData.get('getAddress2'),
                city: formData.get('getCity'),
                country: formData.get('getCountry'),
                email: formData.get('getEmail'),
                phoneNumber: formData.get('getPhone'),
                zipCode: formData.get('getPostal')
            }
        }

        const item = generateSupplier(value)
        await item.updateMain(data)
    }
    async function updateComptabilite(value) {
        const form = document.getElementById('addComptabilite')
        const formData = new FormData(form)

        const dataSociety = {
            accountingAccount: formData.get('accountingAccount'),
            forceVat: formData.get('forceVat'),
            invoiceMin: {
                code: 'EUR',
                value: JSON.parse(formData.get('invoiceMin-value'))
            },
            vat: formData.get('vat'),
            vatMessage: formData.get('vatMessageValue')
        }
        const data = {
            currency: formData.get('currency')
        }
        const item = generateSupplier(value)
        await item.updateAccounting(data)
        // const itemSoc = generateSocieties(value);
        // await itemSoc.update(dataSociety);
        await fetchSocietyStore.update(dataSociety, societyId)
        await fetchSocietyStore.fetchById(societyId)
        //await fetchSocietyStore.fetch();
        managed.value = computed(() => {
            const dataM = {managedCopper: fetchSocietyStore.society.copper.managed}
            return dataM
        })

        list.value = computed(() => ({
            ...fetchSocietyStore.society,
            ...managed.value
        }))

        listSuppliers.value = computed(() => ({
            ...fetchSuppliersStore.supplier,
            ...list.value,
            ...fetchSocietyStore.vatMessageValue
        }))
    }
    async function updateFichiers(value) {
        const suppliersId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)

        const data = {
            category: formData.get('category'),
            file: formData.get('file'),
            supplier: `/api/suppliers/${suppliersId}`
        }
        try {
            await fetchSupplierAttachmentStore.ajout(data)

            isError.value = false
        } catch (error) {
            const err = {
                message: error
            }
            violations.value.push(err)
            isError.value = true
        }
    }
    async function ajout(inputValues) {
        const data = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',

                // phoneNumber: inputValues.getPhone ?? "",
                zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/suppliers/${supplierId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            await fetchSupplierContactsStore.ajout(data, societyId)

            isError2.value = false
        } catch (error) {
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
    async function deleted(id) {
        await fetchSupplierContactsStore.deleted(id)
    }
    async function updateSuppliers(inputValues) {
        const dataUpdate = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',
                // phoneNumber: inputValues.getPhone ?? "",
                zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/suppliers/${supplierId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            const item = generateSupplierContact(inputValues)
            await item.update(dataUpdate)
            isError2.value = false
        } catch (error) {
            await fetchSupplierContactsStore.fetchBySociety(societyId)
            itemsTable.value = fetchSupplierContactsStore.itemsSocieties.reduce(
                (acc, curr) => acc.concat(curr),
                []
            )
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
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
                :component-attribute="fetchSuppliersStore.supplier"
                @update="updateGeneral(fetchSuppliersStore.supplier)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                @update="updateFichiers(fetchSuppliersStore.supplier)"/>
            <div v-if="isError" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <MyTree :node="treeData"/>
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
                @update="update(listSuppliers)"
                @update:model-value="input"/>
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
                @update="updateLogistique(listSuppliers)"
                @update:model-value="input"/>
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
                @update="updateComptabilite(listSuppliers)"/>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresses"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow
                id="addAdresses"
                :fields="Adressefields"
                :component-attribute="fetchSuppliersStore.supplier"
                @update="updateAddress(fetchSuppliersStore.supplier)"/>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppCollectionTable
                v-if="!isShow"
                id="addContacts"
                :fields="fieldsSupp"
                :items="itemsTable"
                @ajout="ajout"
                @deleted="deleted"
                @update="updateSuppliers"/>

            <div v-if="isError2" class="alert alert-danger" role="alert">
                <div v-for="violation in violations2" :key="violation">
                    <li>{{ violation.propertyPath }} {{ violation.message }}</li>
                </div>
            </div>
        </AppTab>
    </AppTabs>
</template>
