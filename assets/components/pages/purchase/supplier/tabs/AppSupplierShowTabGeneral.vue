<script setup>
    import {computed, ref} from 'vue'
    import generateSupplier from '../../../../../stores/supplier/supplier'
    import useOptions from '../../../../../stores/option/options'
    import {useSupplierCompaniesStore} from '../../../../../stores/supplier/supplierCompanies'
    import {useSuppliersStore} from '../../../../../stores/supplier/suppliers'

    const fetchCompanyOptions = useOptions('companies')
    const optionsCompany = computed(() =>
        fetchCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const currentSupplierData = ref({})
    const currentSupplierCompaniesData = ref([])
    const fetchSuppliersStore = useSuppliersStore()
    const fetchSupplierCompaniesStore = useSupplierCompaniesStore()
    async function updateStores() {
        await fetchSuppliersStore.fetchOne(currentSupplierData.value.id)
        await fetchSupplierCompaniesStore.fetchBySupplier(fetchSuppliersStore.supplier)
    }
    function updateLocalData() {
        currentSupplierData.value = fetchSuppliersStore.getSupplier
        currentSupplierCompaniesData.value = fetchSupplierCompaniesStore.supplierCompanies
        currentSupplierData.value.administeredBy = currentSupplierCompaniesData.value.map(item3 => JSON.parse(JSON.stringify(item3.company['@id'])))
    }
    await fetchSupplierCompaniesStore.fetchBySupplier(fetchSuppliersStore.supplier)
    updateLocalData()
    const Géneralitésfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {
            label: 'Compagnies dirigeantes',
            name: 'administeredBy',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'multiselect'
        },
        {label: 'Langue', name: 'language', type: 'text'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
    //Mise à jour de la variable locale en fonction de ce qu'a saisi l'utilisateur
    async function updateLocalSupplierValue(value) {
        currentSupplierData.value = value
    }
    async function updateGeneralApi() {
        //Création des data à passer pour les PATCH API
        const data = {
            language: currentSupplierData.value.language,
            notes: currentSupplierData.value.notes
        }
        const dataAdmin = {
            name: currentSupplierData.value.name
        }
        // Suppression en back des éléments supprimés en front
        currentSupplierCompaniesData.value.forEach(supplierCompany => {
            const currentCompanyIri = supplierCompany.company['@id']
            if (!currentSupplierData.value.administeredBy.includes(currentCompanyIri)) {
                //Il faut alors supprimer supplierCompany en back
                fetchSupplierCompaniesStore.removeOne(supplierCompany.id)
            }
        })
        // Ajout en back des nouveaux éléments
        currentSupplierData.value.administeredBy.forEach(companyIri => {
            let found = false
            currentSupplierCompaniesData.value.forEach(supplierCompany => {
                if (supplierCompany.company['@id'] === companyIri) {
                    found = true
                }
            })
            if (!found) {
                //il faut ajouter companyIri
                fetchSupplierCompaniesStore.addCompany(companyIri)
            }
        })
        //Appels de l'API pour mises à jour
        const item = generateSupplier(currentSupplierData.value)
        await item.updateMain(data)
        await item.updateAdmin(dataAdmin)
        //Rechargement suite à mise à jour
        await updateStores()
        updateLocalData()
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="Géneralitésfields"
        :component-attribute="currentSupplierData"
        @update="updateGeneralApi"
        @update:model-value="updateLocalSupplierValue"/>
</template>

