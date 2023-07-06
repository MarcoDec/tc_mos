<script setup>
    import {computed, onUnmounted, ref} from 'vue'
    import generateCustomer from '../../../../stores/customers/customer'
    import {useCustomerStore} from '../../../../stores/customers/customers'
    import useOptions from '../../../../stores/option/options'

    /*const props = */defineProps({
        dataCustomers: {required: true, type: Object}
    })
    const emit = defineEmits(['update', 'update:modelValue'])
    const fetchCustomerStore = useCustomerStore()
    const fecthCompanyOptions = useOptions('companies')
    await fecthCompanyOptions.fetchOp()
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
        {label: 'Web', name: 'web', type: 'text'},
        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'},
        {label: 'Equivalence', name: 'equivalentEnabled', type: 'boolean'}
    ]
    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)

        const data = {
            equivalentEnabled: JSON.parse(formData.get('equivalentEnabled')),
            language: formData.get('language'),
            notes: formData.get('notes') ? formData.get('notes') : null
        }
        const dataSociety = {
            ar: JSON.parse(formData.get('ar')),
            siren: formData.get('siren'),
            web: formData.get('web')
        }
        const item = generateCustomer(value)
        await item.updateMain(data)

        await fetchSocietyStore.update(dataSociety, societyId)
        await fetchSocietyStore.fetchById(societyId)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
    const val = ref(Number(fetchCustomerStore.customer.administeredBy))
    async function input(value) {
        val.value = value.administeredBy
        emit('update:modelValue', val.value)
        const data = {
            administeredBy: val.value
        }
        const item = generateCustomer(value)
        await item.updateMain(data)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="generalFields"
        :component-attribute="dataCustomers"
        @update="updateGeneral(dataCustomers)"
        @update:model-value="input"/>
</template>

