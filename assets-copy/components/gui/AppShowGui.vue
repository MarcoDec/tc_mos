<script setup>
    import {computed, onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import AppShowGuiCard from './AppShowGuiCard.vue'
    import AppShowGuiResizableCard from './AppShowGuiResizableCard.vue'
    import {GuiRepository} from '../../store/modules'

    const props = defineProps({gui: {required: true, type: Object}})
    const guiBottomTag = computed(() => (
        props.gui.guiBottom === 'AppShowGuiResizableCard'
            ? AppShowGuiResizableCard
            : AppShowGuiCard
    ))
    const {id} = useRouter()
    const guiEl = ref()
    const repo = useRepo(GuiRepository)

    function resizeHandler() {
        if (typeof guiEl.value !== 'undefined')
            repo.resize(id, guiEl.value)
    }

    onMounted(() => {
        window.addEventListener('resize', resizeHandler)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', resizeHandler)
    })

    watchPostEffect(resizeHandler)
</script>

<template>
    <div ref="guiEl" class="bg-secondary gui">
        <div class="gui-top">
            <AppShowGuiCard
                :height="gui.topHeightPx"
                :inner-width="gui.innerWidthPx"
                :margin-end="gui.marginEndPx"
                :width="gui.startWidthPx"
                class="gui-card"
                variant="info">
                <AppTabs id="gui-start" :icon="gui.icon" :vertical="gui.vertical" class="gui-start-content">
                    <AppTab id="gui-start-main" active icon="bars" title="Généralités"/>
                    <AppTab id="gui-start-files" icon="folder" title="Fichiers"/>
                    <AppTab id="gui-start-quality" icon="certificate" title="Qualité"/>
                    <AppTab id="gui-start-purchase-logistics" icon="boxes" title="Achat/Logistique"/>
                    <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Comptabilité"/>
                    <AppTab id="gui-start-addresses" icon="map-marked-alt" title="Adresses"/>
                    <AppTab id="gui-start-contacts" icon="address-card" title="Contacts"/>
                </AppTabs>
            </AppShowGuiCard>
            <AppShowGuiCard
                :height="gui.topHeightPx"
                :inner-width="gui.innerWidthPx"
                :width="gui.endWidthPx"
                class="gui-card gui-end"
                variant="warning"/>
        </div>
        <component
            :is="guiBottomTag"
            :height="gui.bottomHeightPx"
            :inner-width="gui.innerWidthPx"
            :margin-top="gui.marginTopPx"
            :width="gui.innerWidthPx"
            class="gui-card"
            variant="danger">
            <AppTabs id="gui-bottom" :icon="gui.icon" :icon-switch="!gui.icon" class="gui-bottom-content" vertical>
                <AppTab id="gui-bottom-components" active icon="puzzle-piece" title="Fournitures"/>
                <AppTab id="gui-bottom-receipts" icon="receipt" title="Réceptions"/>
                <AppTab id="gui-bottom-orders" icon="shopping-cart" title="Commandes"/>
            </AppTabs>
        </component>
    </div>
</template>

<style scoped>
    .gui {
        max-width: v-bind('gui.widthPx');
        min-width: v-bind('gui.widthPx');
        padding: v-bind('gui.paddingPx');
        width: v-bind('gui.widthPx');
    }

    .gui-bottom-content {
        height: v-bind('gui.innerBottomHeightPx');
        max-height: v-bind('gui.innerBottomHeightPx');
        min-height: v-bind('gui.innerBottomHeightPx');
    }

    .gui-card {
        padding: v-bind('gui.paddingPx');
    }

    .gui-start-content {
        height: v-bind('gui.innerStartHeightPx');
        max-height: v-bind('gui.innerStartHeightPx');
        min-height: v-bind('gui.innerStartHeightPx');
    }

    @media (max-width: 1140px) {
        .gui-end {
            margin-top: v-bind('gui.marginTopPx') !important;
        }
    }

    @media (min-width: 1140px) {
        .gui {
            height: v-bind('gui.heightPx');
            max-height: v-bind('gui.heightPx');
            min-height: v-bind('gui.heightPx');
        }
    }
</style>
