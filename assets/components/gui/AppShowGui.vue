<script lang="ts" setup>
    import type {Getters, Mutations} from '../../store/gui'
    import {onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'
    import type {DeepReadonly} from '../../types/types'
    import {MutationTypes} from '../../store/gui'

    const gui = ref<DeepReadonly<HTMLDivElement>>()
    const {
        bottomHeightPx,
        endWidthPx,
        guiBottom,
        heightPx,
        innerBottomHeightPx,
        innerStartHeightPx,
        innerWidthPx,
        marginEndPx,
        paddingPx,
        startWidthPx,
        topHeightPx,
        marginTopPx,
        widthPx
    } = useNamespacedGetters<Getters>('gui', [
        'bottomHeightPx',
        'endWidthPx',
        'guiBottom',
        'heightPx',
        'innerBottomHeightPx',
        'innerStartHeightPx',
        'innerWidthPx',
        'marginEndPx',
        'paddingPx',
        'startWidthPx',
        'topHeightPx',
        'marginTopPx',
        'widthPx'
    ])
    const {[MutationTypes.RESIZE]: resize} = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])

    function resizeHandler(): void {
        if (typeof gui.value !== 'undefined')
            resize(gui.value)
    }

    watchPostEffect(() => {
        resizeHandler()
    })

    onMounted(() => {
        window.addEventListener('resize', resizeHandler)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', resizeHandler)
    })
</script>

<template>
    <div ref="gui" class="bg-secondary gui">
        <div class="gui-top">
            <AppShowGuiCard
                :height="topHeightPx"
                :inner-width="innerWidthPx"
                :margin-end="marginEndPx"
                :width="startWidthPx"
                bg-variant="info"
                class="gui-card">
                <AppTabs id="gui-start" class="gui-start-content">
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
                :height="topHeightPx"
                :inner-width="innerWidthPx"
                :width="endWidthPx"
                bg-variant="warning"
                class="gui-card gui-end"/>
        </div>
        <component
            :is="guiBottom"
            :height="bottomHeightPx"
            :inner-width="innerWidthPx"
            :margin-top="marginTopPx"
            :width="innerWidthPx"
            bg-variant="danger"
            class="gui-card">
            <AppTabs id="gui-bottom" class="gui-bottom-content" icon-switch vertical>
                <AppTab id="gui-bottom-components" active icon="puzzle-piece" title="Fournitures"/>
                <AppTab id="gui-bottom-receipts" icon="receipt" title="Réceptions"/>
                <AppTab id="gui-bottom-orders" icon="shopping-cart" title="Commandes"/>
            </AppTabs>
        </component>
    </div>
</template>

<style scoped>
.gui {
  max-width: v-bind('widthPx');
  min-width: v-bind('widthPx');
  padding: v-bind('paddingPx');
  width: v-bind('widthPx');
}

.gui-bottom-content {
  height: v-bind('innerBottomHeightPx');
  max-height: v-bind('innerBottomHeightPx');
  min-height: v-bind('innerBottomHeightPx');
}

.gui-card {
  padding: v-bind('paddingPx');
}

.gui-start-content {
  height: v-bind('innerStartHeightPx');
  max-height: v-bind('innerStartHeightPx');
  min-height: v-bind('innerStartHeightPx');
}

@media (max-width: 1140px) {
  .gui-end {
    margin-top: v-bind('marginTopPx') !important;
  }
}

@media (min-width: 1140px) {
  .gui {
    height: v-bind('heightPx');
    max-height: v-bind('heightPx');
    min-height: v-bind('heightPx');
  }
}
</style>
