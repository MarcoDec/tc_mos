<script setup>
    import AppEmployeeFormShow from './AppEmployeeFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppEmployeeShowInlist from './bottom/AppEmployeeShowInlist.vue'
    import {useEmployeeStore} from '../../../../stores/employee/employees'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idEmployee = Number(route.params.id_employee)
    const fetchEmployeeStore = useEmployeeStore()
    fetchEmployeeStore.fetchOne(idEmployee)
    fetchEmployeeStore.fetchAll()
    fetchEmployeeStore.fetchTeams()
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div class="bg-white border-1 border-dark">
                <b>Employee ({{ fetchEmployeeStore.employee.id }})</b>: {{ fetchEmployeeStore.employee.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppEmployeeFormShow v-if="fetchEmployeeStore.isLoaded && fetchEmployeeStore.teamsIsLoaded"/></AppSuspense>
        </template>
        <template #gui-bottom>
            <AppSuspense>
                <AppEmployeeShowInlist/>
            </AppSuspense>
        </template>
        <template #gui-right>
            <!--            {{ route.params.id_employee }}-->
        </template>
    </AppShowGuiGen>
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>

