<script setup>
    import generateEmployee, {baseHierarchie, baseAccounting, basePurchase, baseProduction, baseLogistics, baseQuality, baseSelling, baseMaintenance, baseIt, baseHr, baseManagement, baseProject} from '../../../../../stores/hr/employee/employee'
    import {ref} from 'vue'
    import {useEmployeeStore} from '../../../../../stores/hr/employee/employees'
    const emit = defineEmits(['update', 'update:modelValue'])
    const fetchEmployeeStore = useEmployeeStore()
    await fetchEmployeeStore.fetchTeams()
    const optionsHierarchie = baseHierarchie.map(item => ({
        text: item,
        value: item
    }))
    const optionsAccounting = baseAccounting.map(item => ({
        text: item,
        value: item
    }))
    const optionsProduction = baseProduction.map(item => ({
        text: item,
        value: item
    }))
    const optionsQuality = baseQuality.map(item => ({
        text: item,
        value: item
    }))
    const optionsPurchase = basePurchase.map(item => ({
        text: item,
        value: item
    }))
    const optionsSelling = baseSelling.map(item => ({
        text: item,
        value: item
    }))
    const optionsLogistics = baseLogistics.map(item => ({
        text: item,
        value: item
    }))
    const optionsMaintenance = baseMaintenance.map(item => ({
        text: item,
        value: item
    }))
    const optionsIt = baseIt.map(item => ({
        text: item,
        value: item
    }))
    const optionsHr = baseHr.map(item => ({
        text: item,
        value: item
    }))
    const optionsManagement = baseManagement.map(item => ({
        text: item,
        value: item
    }))
    const optionsProject = baseProject.map(item => ({
        text: item,
        value: item
    }))
    const droitFields = [
        {
            label: 'Hierarchie',
            name: 'getHierarchieRole',
            options: {
                label: value =>
                    optionsHierarchie.find(option => option.value === value)?.text ?? null,
                options: optionsHierarchie
            },
            type: 'multiselect'
        },
        {
            label: 'Accounting',
            name: 'getAccountingRole',
            options: {
                label: value =>
                    optionsAccounting.find(option => option.value === value)?.text ?? null,
                options: optionsAccounting
            },
            type: 'multiselect'
        },
        {
            label: 'Production',
            name: 'getProductionRole',
            options: {
                label: value =>
                    optionsProduction.find(option => option.value === value)?.text ?? null,
                options: optionsProduction
            },
            type: 'multiselect'
        },
        {
            label: 'Quality',
            name: 'getQualityRole',
            options: {
                label: value =>
                    optionsQuality.find(option => option.value === value)?.text ?? null,
                options: optionsQuality
            },
            type: 'multiselect'
        },
        {
            label: 'Purchase',
            name: 'getPurchaseRole',
            options: {
                label: value =>
                    optionsPurchase.find(option => option.value === value)?.text ?? null,
                options: optionsPurchase
            },
            type: 'multiselect'
        },
        {
            label: 'Selling',
            name: 'getSellingRole',
            options: {
                label: value =>
                    optionsSelling.find(option => option.value === value)?.text ?? null,
                options: optionsSelling
            },
            type: 'multiselect'
        },
        {
            label: 'Logistics',
            name: 'getLogisticsRole',
            options: {
                label: value =>
                    optionsLogistics.find(option => option.value === value)?.text ?? null,
                options: optionsLogistics
            },
            type: 'multiselect'
        },
        {
            label: 'Maintenance',
            name: 'getMaintenanceRole',
            options: {
                label: value =>
                    optionsMaintenance.find(option => option.value === value)?.text ?? null,
                options: optionsMaintenance
            },
            type: 'multiselect'
        },
        {
            label: 'It',
            name: 'getItRole',
            options: {
                label: value =>
                    optionsIt.find(option => option.value === value)?.text ?? null,
                options: optionsIt
            },
            type: 'multiselect'
        },
        {
            label: 'Hr',
            name: 'getHrRole',
            options: {
                label: value =>
                    optionsHr.find(option => option.value === value)?.text ?? null,
                options: optionsHr
            },
            type: 'multiselect'
        },
        {
            label: 'Management',
            name: 'getManagementRole',
            options: {
                label: value =>
                    optionsManagement.find(option => option.value === value)?.text ?? null,
                options: optionsManagement
            },
            type: 'multiselect'
        },
        {
            label: 'Project',
            name: 'getProjectRole',
            options: {
                label: value =>
                    optionsProject.find(option => option.value === value)?.text ?? null,
                options: optionsProject
            },
            type: 'multiselect'
        }
    ]
    // const valRole = ref(fetchEmployeeStore.employee.embRoles.roles)
    const valRole = ref({})
    valRole.value = fetchEmployeeStore.employee.embRoles.roles
    async function inputRole(value) {
        // console.log('inputRole', value)
        // console.log('valRole before', valRole.value)
        valRole.value = value.getHierarchieRole.concat(
            value.getAccountingRole,
            value.getProductionRole,
            value.getQualityRole,
            value.getPurchaseRole,
            value.getSellingRole,
            value.getLogisticsRole,
            value.getMaintenanceRole,
            value.getItRole,
            value.getHrRole,
            value.getManagementRole,
            value.getProjectRole
        )
        // console.log('valRole after', valRole.value)
        emit('update:modelValue', valRole.value)
    }
    async function update() {
        // console.log('update')
        const data = {
            embRoles: {
                roles: valRole.value
            }
        }
        const item = generateEmployee(fetchEmployeeStore.employee)
        await item.updateIt(data)
    }
</script>

<template>
    <AppCardShow
        id="addDroits"
        :fields="droitFields"
        :component-attribute="fetchEmployeeStore.employee"
        @update:model-value="inputRole"
        @update="update"/>
</template>

