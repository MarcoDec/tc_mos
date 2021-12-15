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
                <AppTabs class="gui-start-content">
                    <AppTab id="main" active title="Général"/>
                    <AppTab id="logistics" title="Logistique"/>
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
            class="gui-card"/>
    </div>
</template>

<style scoped>
    .gui {
        max-width: v-bind('widthPx');
        min-width: v-bind('widthPx');
        padding: v-bind('paddingPx');
        width: v-bind('widthPx');
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
