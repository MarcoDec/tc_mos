<script setup>
    import AppEmployeeFormShow from './AppEmployeeFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {useEmployeeStore} from '../../../../stores/hr/employee/employees'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idEmployee = Number(route.params.id_employee)
    const fetchEmployeeStore = useEmployeeStore()
    fetchEmployeeStore.fetchOne(idEmployee)
    fetchEmployeeStore.fetchAll()
    fetchEmployeeStore.fetchTeams()
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div class="bg-white">
                    <b>Employee ({{ fetchEmployeeStore.employee.id }})</b>: {{ fetchEmployeeStore.employee.name }}
                </div>
            </template>
            <template #gui-left>
                <AppSuspense><AppEmployeeFormShow v-if="fetchEmployeeStore.isLoaded && fetchEmployeeStore.teamsIsLoaded"/></AppSuspense>
            </template>
            <template #gui-bottom>
                <!--            <AppTabs id="gui-bottom">-->
                <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
                <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
                <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
                <!--            </AppTabs>-->
            </template>
            <template #gui-right>
                <!--            {{ route.params.id_employee }}-->
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>
