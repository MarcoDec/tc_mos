<script setup>
    import generateEmployee from '../../../../../stores/hr/employee/employee'
    import {useEmployeeStore} from '../../../../../stores/hr/employee/employees'
    const fetchEmployeeStore = useEmployeeStore()
    const generalFields = [{label: 'Note', name: 'notes', type: 'textarea'}]
    async function updateGeneral(value) {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            notes: formData.get('notes') ? formData.get('notes') : null
        }
        const item = generateEmployee(value)
        await item.update(data)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="generalFields"
        :component-attribute="fetchEmployeeStore.employee"
        @update="updateGeneral(fetchEmployeeStore.employee)"/>
</template>
