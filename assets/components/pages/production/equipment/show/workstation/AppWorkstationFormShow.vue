<script setup>
    // import AppShowWorkstationTabGeneral from './tabs/AppShowWorkstationTabGeneral.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppTabFichiers from '../../../../../tab/AppTabFichiers.vue'
    import {useEngineAttachmentStore} from '../../../../../../stores/production/engine/workstation/engineAttachment'
    import {useRoute} from 'vue-router'
    import useUser from '../../../../../../stores/security'
    import {useWorkstationsStore} from '../../../../../../stores/production/engine/workstation/workstations'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    import AppWorkstationTabMaintenance from "./tabs/AppWorkstationTabMaintenance.vue";
    import AppWorkstationTabHistMaintenance from "./tabs/AppWorkstationTabHistMaintenance.vue";
    const currentCompany = useUser().company
    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchEngineStore = useWorkstationsStore()
    //region récupération des pièces jointes
    const fetchEngineAttachmentStore = useEngineAttachmentStore()
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
                    :element-api-url="`/api/workstations/${fetchEngineStore.engine.id}`"
                    :element-attachment-store="fetchEngineAttachmentStore"
                    :element-id="fetchEngineStore.engine.id"
                    element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                    :element-store="useWorkstationsStore"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-maintenances" icon="paint-roller" title="Types de maintenances" tabs="gui-start">
            <AppSuspense><AppWorkstationTabMaintenance/></AppSuspense>
        </AppTab>
        <AppTab id="gui-start-history" icon="history" title="Historique des maintenances" tabs="gui-start">
            <AppSuspense><AppWorkstationTabHistMaintenance/></AppSuspense>
        </AppTab>
        <AppTab id="gui-machines" icon="cogs" title="Machines" tabs="gui-start">
            TODO
        </AppTab>
        <AppTab id="gui-spareParts" icon="puzzle-piece" title="Pièces de rechange" tabs="gui-start">
            TODO
        </AppTab>
        <!--        <AppTab id="gui-start-quality" title="Qualité" icon="certificate" tabs="gui-start">-->
        <!--            <AppCardShow id="addQualite" :fields="qualityFields" :component-attribute="fetchEngineStore.engine"/>-->
        <!--        </AppTab>-->
        <!--        <AppTab id="gui-start-purchase-logistics" title="Logistique" icon="pallet" tabs="gui-start">-->
        <!--            <AppCardShow id="addLogistique" :component-attribute="fetchEngineStore.engine"/>-->
        <!--        </AppTab>-->
        <!--        <AppTab id="gui-start-addresses" title="Adresses\Contacts" icon="file-contract" tabs="gui-start">-->
        <!--            <AppCardShow id="addContacts" :fields="Contactsfields" :component-attribute="fetchEngineStore.engine"/>-->
        <!--        </AppTab>-->
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
