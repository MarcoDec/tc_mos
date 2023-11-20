<script setup>
    import {computed, onUnmounted, ref, toRefs} from 'vue'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import useOptions from '../../../../../stores/option/options'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'

    const {dataCustomers} = toRefs(defineProps({
        dataCustomers: {required: true, type: Object}
    }))
    const localData = ref({})
    const societyId = ref(0)
    const fetchCustomerStore = useCustomerStore()
    const fecthCompanyOptions = useOptions('companies')
    const fetchSocietyStore = useSocietyStore()
    await fecthCompanyOptions.fetchOp()
    societyId.value = dataCustomers.value.society.split('/')[3]
    await fetchSocietyStore.fetchById(societyId.value)
    localData.value = {
        administeredBy: dataCustomers.value.administeredBy,
        ar: fetchSocietyStore.society.ar,
        equivalentEnabled: dataCustomers.value.equivalentEnabled,
        language: dataCustomers.value.language,
        notes: dataCustomers.value.notes,
        siren: fetchSocietyStore.society.siren
    }
    onUnmounted(() => {
        fecthCompanyOptions.dispose()
    })
    const optionsCompany = computed(() =>
        fecthCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            return {text, value}
        }))
    const generalFields = [
        {label: 'Langue', name: 'language', type: 'text'},
        {label: 'SIREN', name: 'siren', type: 'text'},
        {
            label: 'Compagnies',
            name: 'administeredBy',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'multiselect'
        },
        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'},
        {label: 'Equivalence', name: 'equivalentEnabled', type: 'boolean'}
    ]
    async function updateGeneral() {
        const data = {
            administeredBy: localData.value.administeredBy,
            equivalentEnabled: localData.value.equivalentEnabled,
            language: localData.value.language,
            notes: localData.value.notes
        }
        const dataSociety = {
            ar: localData.value.ar,
            siren: localData.value.siren
        }
        const item = generateCustomer(dataCustomers.value)
        await item.updateMain(dataCustomers.value.id, data)

        await fetchSocietyStore.update(dataSociety, societyId.value)
        await fetchSocietyStore.fetchById(societyId.value)
        await fetchCustomerStore.fetchOne(dataCustomers.value.id)
    }
    //const val = ref(Number(fetchCustomerStore.customer.administeredBy))
    async function input(value) {
        localData.value = value
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="generalFields"
        :component-attribute="localData"
        @update="updateGeneral"
        @update:model-value="input"/>
</template>

