<script setup>
    import AppShowEmployeeContact from '../../../components/pages/hr/employee/AppShowEmployeeContact.vue'
    import AppShowEmployeeTabAccess from '../../../components/pages/hr/employee/AppShowEmployeeTabAccess.vue'
    import AppShowEmployeeTabDroits from '../../../components/pages/hr/employee/AppShowEmployeeTabDroits.vue'
    import AppShowEmployeeTabFichiers from '../../../components/pages/hr/employee/AppShowEmployeeTabFichiers.vue'
    import AppShowEmployeeTabGeneral from '../../../components/pages/hr/employee/AppShowEmployeeTabGeneral.vue'
    import AppShowEmployeeTabInfos from '../../../components/pages/hr/employee/AppShowEmployeeTabInfos.vue'
    import AppShowEmployeeTabProduction from '../../../components/pages/hr/employee/AppShowEmployeeTabProduction.vue'
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabGeneral/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-Informations"
            title="Informations personelles"
            icon="circle-info"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabInfos/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <Suspense><AppShowEmployeeContact/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-droits"
            title="Droits"
            icon="clipboard-check"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabDroits/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-accés"
            title="Accés"
            icon="arrow-right-to-bracket"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabAccess/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabFichiers/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="industry"
            tabs="gui-start">
            <Suspense><AppShowEmployeeTabProduction/></Suspense>
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
