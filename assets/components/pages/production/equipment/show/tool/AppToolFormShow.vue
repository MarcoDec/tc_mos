<script setup>
    // import AppShowToolTabGeneral from './AppShowToolTabGeneral.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppTabFichiers from '../../../../../tab/AppTabFichiers.vue'
    import {useEngineAttachmentStore} from '../../../../../../stores/production/engine/tool/engineAttachment'
    import {useRoute} from 'vue-router'
    import {useToolsStore} from '../../../../../../stores/production/engine/tool/tools'
    import useUser from '../../../../../../stores/security'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    const currentCompany = useUser().company
    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchEngineAttachmentStore = useEngineAttachmentStore()
    await fetchEngineAttachmentStore.fetchByElement(idEngine)
    const fetchEngineStore = useToolsStore()
    const fetchZones = useZonesStore()
    await fetchZones.fetchAll(currentCompany)
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
                    attachment-element-label="tool"
                    :element-api-url="`/api/tools/${fetchEngineStore.engine.id}`"
                    :element-attachment-store="fetchEngineAttachmentStore"
                    :element-id="fetchEngineStore.engine.id"
                    element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                    :element-store="useToolsStore"/>
            </AppSuspense>
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
