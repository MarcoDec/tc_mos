<script setup>
    import generateEmployee from '../../../../stores/employee/employee'
    import {ref} from 'vue'
    import {useEmployeeStore} from '../../../../stores/employee/employees'
    const emit = defineEmits(['update', 'update:modelValue'])
    const fetchEmployeeStore = useEmployeeStore()
    await fetchEmployeeStore.fetchTeams()
    const optionsRoles = [
        {text: 'ROLE_USER', value: 'ROLE_USER'},
        {text: 'ROLE_PRODUCTION_WRITER', value: 'ROLE_PRODUCTION_WRITER'}
    ]
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

