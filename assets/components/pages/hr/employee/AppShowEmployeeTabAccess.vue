<script setup>
    import {computed} from 'vue'
    import generateEmployee from '../../../../stores/employee/employee'
    import {useEmployeeStore} from '../../../../stores/employee/employees'
    import useOptions from '../../../../stores/option/options'

    const fetchEmployeeStore = useEmployeeStore()
    const fecthCompanyOptions = useOptions('companies')
    await fecthCompanyOptions.fetchOp()
    const optionsCompany = computed(() =>
        fecthCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            return {text, value}
        }))
    const accessFields = [
        {label: 'Identifiant', name: 'username', type: 'text'},
        {label: 'Mot de passe', name: 'plainPassword', type: 'text'},
        {label: 'Badge', name: 'timeCard', type: 'text'},
        {
            label: 'Compagnie',
            name: 'company',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'select'
        },
        {label: 'Activation', name: 'userEnabled', type: 'boolean'}
    ]
    async function updateAcces(value) {
        const form = document.getElementById('addAccés')
        const formData = new FormData(form)
        const data = {
            company: formData.get('company'),
            plainPassword: formData.get('plainPassword'),
            timeCard: formData.get('timeCard'),
            userEnabled: JSON.parse(formData.get('userEnabled')),
            username: formData.get('username')
        }
        const item = generateEmployee(value)
        await item.updateIt(data)
    }
</script>

<template>
    <AppCardShow
        id="addAccés"
        :fields="accessFields"
        :component-attribute="fetchEmployeeStore.employee"
        @update="updateAcces(fetchEmployeeStore.employee)"/>
</template>

