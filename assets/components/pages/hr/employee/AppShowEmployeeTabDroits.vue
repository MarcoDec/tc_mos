<script setup>
    import generateEmployee from '../../../../stores/employee/employee'
    import {ref} from 'vue'
    import {useEmployeeStore} from '../../../../stores/employee/employees'
    const emit = defineEmits(['update', 'update:modelValue'])
    const fetchEmployeeStore = useEmployeeStore()
    await fetchEmployeeStore.fetchTeams()
    const baseRoles = [
        'ROLE_ACCOUNTING_READER',
        'ROLE_ACCOUNTING_WRITER',
        'ROLE_ACCOUNTING_ADMIN',
        'ROLE_HR_READER',
        'ROLE_HR_WRITER',
        'ROLE_HR_ADMIN',
        'ROLE_IT_ADMIN',
        'ROLE_LEVEL_ANIMATOR',
        'ROLE_LEVEL_DIRECTOR',
        'ROLE_LEVEL_MANAGER',
        'ROLE_LEVEL_OPERATOR',
        'ROLE_LOGISTICS_READER',
        'ROLE_LOGISTICS_WRITER',
        'ROLE_LOGISTICS_ADMIN',
        'ROLE_MAINTENANCE_READER',
        'ROLE_MAINTENANCE_WRITER',
        'ROLE_MAINTENANCE_ADMIN',
        'ROLE_MANAGEMENT_READER',
        'ROLE_MMANAGEMENT_WRITER',
        'ROLE_MANAGEMENT_ADMIN',
        'ROLE_PRODUCTION_READER',
        'ROLE_PRODUCTION_WRITER',
        'ROLE_PRODUCTION_ADMIN',
        'ROLE_PROJECT_READER',
        'ROLE_PROJECT_WRITER',
        'ROLE_PROJECT_ADMIN',
        'ROLE_PURCHASE_READER',
        'ROLE_PURCHASE_WRITER',
        'ROLE_PURCHASE_ADMIN',
        'ROLE_QUALITY_READER',
        'ROLE_QUALITY_WRITER',
        'ROLE_QUALITY_ADMIN',
        'ROLE_SELLING_READER',
        'ROLE_SELLING_WRITER',
        'ROLE_SELLING_ADMIN'
    ]
    const optionsRoles = baseRoles.map(item => ({
        text: item,
        value: item
    }))
    const droitFields = [
        {
            label: 'Role',
            name: 'getEmbRoles',
            options: {
                label: value =>
                    optionsRoles.find(option => option.type === value)?.text ?? null,
                options: optionsRoles
            },
            type: 'multiselect'
        }
    ]
    const valRole = ref(fetchEmployeeStore.employee.embRoles.roles)
    async function inputRole(value) {
        valRole.value = value.getEmbRoles
        emit('update:modelValue', valRole.value)

        const data = {
            embRoles: {
                roles: valRole.value
            }
        }

        const item = generateEmployee(value)

        await item.updateIt(data)
    }
</script>

<template>
    <AppCardShow
        id="addDroits"
        :fields="droitFields"
        :component-attribute="fetchEmployeeStore.employee"
        @update:model-value="inputRole"/>
</template>

