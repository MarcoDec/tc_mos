<script lang="ts" setup>
    import {ActionTypes, MutationTypes} from '../../store/gui'
    import type {Actions, Getters, Mutations} from '../../store/gui'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'

    const {
        bottomHeightPx,
        containerWidthPx,
        topInnerHeightPx
    } = useNamespacedGetters<Getters>('gui', [
        'bottomHeightPx',
        'containerWidthPx',
        'topInnerHeightPx'
    ])
    const {[MutationTypes.ENABLE_RESIZE]: enableResize} = useNamespacedMutations<Mutations>('gui', [MutationTypes.ENABLE_RESIZE])
    const actions = useNamespacedActions<Actions>('gui', [ActionTypes.INIT_DRAG])
    const initDrag = actions[ActionTypes.INIT_DRAG] as (e: MouseEvent) => Promise<void>
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
