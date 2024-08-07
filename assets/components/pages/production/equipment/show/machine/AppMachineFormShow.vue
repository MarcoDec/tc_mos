<script setup>
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppTabFichiers from '../../../../../tab/AppTabFichiers.vue'
    import {useRoute} from 'vue-router'
    import useUser from '../../../../../../stores/security'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    import {useGenEngineAttachmentStore} from '../../../../../../stores/production/engine/generic/engineAttachment'
    import {useGenEngineStore} from '../../../../../../stores/production/engine/generic/engines'
    import AppMachineTabMaintenance from './tabs/AppMachineTabMaintenance.vue'
    import AppMachineTabHistMaintenance from './tabs/AppMachineTabHistMaintenance.vue'
    const currentCompany = useUser().company
    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchEngineStore = useGenEngineStore('machines', '/api/machines')()
    //region récupération des pièces jointes
    const fetchEngineAttachmentStore = useGenEngineAttachmentStore()()
    await fetchEngineAttachmentStore.fetchByElement(idEngine)
    //endregion
    //region récupération des zones liées à la compangnie
    const fetchZones = useZonesStore()
    await fetchZones.fetchAll(currentCompany)
    //endregion
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-files"
            active
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppSuspense>
                <AppTabFichiers
                    attachment-element-label="engine"
                    :element-api-url="`/api/machines/${fetchEngineStore.engine.id}`"
                    :element-attachment-store="fetchEngineAttachmentStore"
                    :element-id="fetchEngineStore.engine.id"
                    element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                    :element-store="useGenEngineStore('machines', '/api/machines')"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-maintenances" icon="paint-roller" title="Types de maintenances" tabs="gui-start">
            <AppSuspense><AppMachineTabMaintenance/></AppSuspense>
        </AppTab>
        <AppTab id="gui-start-history" icon="history" title="Historique des maintenances" tabs="gui-start">
            <AppSuspense><AppMachineTabHistMaintenance/></AppSuspense>
        </AppTab>
        <AppTab id="gui-machines" icon="cogs" title="Machines" tabs="gui-start">
            TODO
        </AppTab>
        <AppTab id="gui-spareParts" icon="puzzle-piece" title="Pièces de rechange" tabs="gui-start">
            TODO
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
