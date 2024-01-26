<script setup>
    import AppShowCounterPartTabGeneral from './AppShowCounterPartTabGeneral.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppTabFichiers from '../../../../../tab/AppTabFichiers.vue'
    import {useCounterPartStore} from '../../../../../../stores/production/engine/test-counter-part/testCounterPart'
    import {useEngineAttachmentStore} from '../../../../../../stores/production/engine/test-counter-part/engineAttachment'
    import {useRoute} from 'vue-router'
    import useUser from '../../../../../../stores/security'
    import useZonesStore from '../../../../../../stores/production/company/zones'
    const currentCompany = useUser().company
    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchEngineAttachmentStore = useEngineAttachmentStore()
    await fetchEngineAttachmentStore.fetchByElement(idEngine)
    const fetchEngineStore = useCounterPartStore()
    const fetchZones = useZonesStore()
    await fetchZones.fetchAll(currentCompany)
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil" tabs="gui-start">
            <AppSuspense><AppShowCounterPartTabGeneral v-if="fetchEngineStore.isLoaded"/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppSuspense>
                <AppTabFichiers
                    attachment-element-label="counter-part"
                    :element-api-url="`/api/counter-parts/${fetchEngineStore.engine.id}`"
                    :element-attachment-store="fetchEngineAttachmentStore"
                    :element-id="fetchEngineStore.engine.id"
                    element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                    :element-store="useCounterPartStore"/>
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
