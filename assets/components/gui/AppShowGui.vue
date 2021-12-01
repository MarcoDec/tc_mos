<script lang="ts" setup>
    import type {Getters, Mutations} from '../../store/gui'
    import {MutationTypes} from '../../store/gui'
    import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'

    const {
        bottomHeightPx,
        containerWidthPx,
        topInnerHeightPx
    } = useNamespacedGetters<Getters>('gui', [
        'bottomHeightPx',
        'containerWidthPx',
        'topInnerHeightPx'
    ])
    const {
        [MutationTypes.ENABLE_RESIZE]: enableResize,
        [MutationTypes.INIT_DRAG]: initDrag
    } = useNamespacedMutations<Mutations>('gui', [
        MutationTypes.ENABLE_RESIZE,
        MutationTypes.INIT_DRAG
    ])
</script>

<template>
    <AppRow class="gui">
        <AppCol>
            <AppContainer class="gui-container mt-2" fluid>
                <AppRow>
                    <AppShowGuiCard bg-variant="info"/>
                    <AppShowGuiCard bg-variant="warning"/>
                </AppRow>
                <hr class="resizer" @click="enableResize" @mousedown="initDrag"/>
                <AppRow>
                    <AppShowGuiCard bg-variant="success" card-class="gui-bottom"/>
                </AppRow>
            </AppContainer>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .gui-bottom {
        height: v-bind('topInnerHeightPx');
        min-height: v-bind('bottomHeightPx');
        max-height: v-bind('topInnerHeightPx')
    }

    .gui {
        width: v-bind('containerWidthPx')
    }
</style>
