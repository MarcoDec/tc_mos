<script setup>
    import {computed, ref} from 'vue'
    import generateSupplier from '../../../../stores/supplier/supplier'
    import useOptions from '../../../../stores/option/options'
    import {useSuppliersStore} from '../../../../stores/supplier/suppliers'

    const props = defineProps({
        supplierId: {required: true, type: String}
    })
    const fetchCompanyOptions = useOptions('companies')
    const optionsCompany = computed(() =>
        fetchCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const currentSupplierData = ref({})
    const fetchSuppliersStore = useSuppliersStore()
    currentSupplierData.value = fetchSuppliersStore.supplier
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
            administeredBy: currentSupplierData.value.administeredBy,
            name: currentSupplierData.value.name
        }
        //Appels de l'API pour mises à jour
        const item = generateSupplier(currentSupplierData.value)
        await item.updateMain(data)
        await item.updateAdmin(dataAdmin)
        //Rechargement suite à mise à jour
        await fetchSuppliersStore.fetchOne(props.supplierId)
        //Réinitialisation variable locale suite à la mise à jour
        currentSupplierData.value = fetchSuppliersStore.supplier
    }
</script>

<template>
    <AppTab
        id="gui-start-main"
        active
        title="Généralités"
        icon="pencil"
        tabs="gui-start">
        <AppCardShow
            id="addGeneralites"
            :fields="Géneralitésfields"
            :component-attribute="currentSupplierData"
            @update="updateGeneralApi"
            @update:model-value="updateLocalSupplierValue"/>
    </AppTab>
</template>

