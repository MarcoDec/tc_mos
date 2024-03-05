<script setup>
    import AppShowEmployeeContact from './tabs/AppShowEmployeeTabContact.vue'
    import AppShowEmployeeTabAccess from './tabs/AppShowEmployeeTabAccess.vue'
    import AppShowEmployeeTabDroits from './tabs/AppShowEmployeeTabDroits.vue'
    import AppShowEmployeeTabInfos from './tabs/AppShowEmployeeTabInfos.vue'
    import AppShowEmployeeTabProduction from './tabs/AppShowEmployeeTabProduction.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'
    import {useEmployeeAttachmentStore} from '../../../../stores/hr/employee/employeeAttachements'
    import {useEmployeeStore} from '../../../../stores/hr/employee/employees'
    import {useRoute} from 'vue-router'
    const route = useRoute()
    const employeeId = route.params.id_employee
    const employeeAttachmentStore = useEmployeeAttachmentStore()
    employeeAttachmentStore.fetchByElement(employeeId)
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-Informations"
            active
            title="Informations personelles"
            icon="circle-info"
            tabs="gui-start">
            <AppSuspense><AppShowEmployeeTabInfos/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppSuspense><AppShowEmployeeContact/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-droits"
            title="Droits"
            icon="clipboard-check"
            tabs="gui-start">
            <AppSuspense><AppShowEmployeeTabDroits/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-accés"
            title="Accés"
            icon="arrow-right-to-bracket"
            tabs="gui-start">
            <AppSuspense><AppShowEmployeeTabAccess/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppSuspense>
                <AppTabFichiers
                    attachment-element-label="employee"
                    :element-api-url="`/api/employees/${employeeId}`"
                    :element-attachment-store="employeeAttachmentStore"
                    :element-id="employeeId"
                    element-parameter-name="EMPLOYEE_ATTACHMENT_CATEGORIES"
                    :element-store="useEmployeeStore"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Organisation"
            icon="industry"
            tabs="gui-start">
            <AppSuspense><AppShowEmployeeTabProduction/></AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
    #gui-start-production, #gui-start-droits {
        padding-bottom: 150px;
    }
</style>
