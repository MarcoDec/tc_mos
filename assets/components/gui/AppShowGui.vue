<script lang="ts" setup>
    import type {Getters, Mutations} from '../../store/gui'
    import {onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'
    import {MutationTypes} from '../../store/gui'

    const gui = ref<HTMLDivElement>()
    const {
        bottomHeightPx,
        endWidthPx,
        heightPx,
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
        'heightPx',
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
    <div ref="gui" class="bg-secondary gui overflow-hidden">
        <div class="d-flex">
            <AppShowGuiCard :height="topHeightPx" :margin-end="marginEndPx" :width="startWidthPx" bg-variant="info"/>
            <AppShowGuiCard :height="topHeightPx" :width="endWidthPx" bg-variant="warning" class="d-inline"/>
        </div>
        <AppShowGuiResizableCard
            :height="bottomHeightPx"
            :margin-top="marginTopPx"
            :width="innerWidthPx"
            bg-variant="danger"/>
    </div>
</template>

<style scoped>
    .gui {
        height: v-bind('heightPx');
        max-height: v-bind('heightPx');
        max-width: v-bind('widthPx');
        min-height: v-bind('heightPx');
        min-width: v-bind('widthPx');
        padding: v-bind('paddingPx');
        width: v-bind('widthPx')
    }
</style>
