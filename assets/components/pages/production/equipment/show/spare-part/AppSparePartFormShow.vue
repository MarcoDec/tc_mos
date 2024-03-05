<script setup>
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppTabFichiers from '../../../../../tab/AppTabFichiers.vue'
    import {useRoute} from 'vue-router'
    import useUser from '../../../../../../stores/security'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    import {useGenEngineAttachmentStore} from '../../../../../../stores/production/engine/generic/engineAttachment'
    import {useGenEngineStore} from '../../../../../../stores/production/engine/generic/engines'
    const currentCompany = useUser().company
    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const enginesStr = 'spare-parts'
    const baseUrl = '/api/spare-parts'

    const fetchEngineStore = useGenEngineStore(enginesStr, baseUrl)()
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
                    :element-api-url="`${baseUrl}/${fetchEngineStore.engine.id}`"
                    :element-attachment-store="fetchEngineAttachmentStore"
                    :element-id="fetchEngineStore.engine.id"
                    element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                    :element-store="useGenEngineStore(enginesStr, baseUrl)"/>
            </AppSuspense>
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
