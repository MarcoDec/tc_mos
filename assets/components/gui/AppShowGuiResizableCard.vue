<script lang="ts" setup>
    import {ActionTypes, MutationTypes} from '../../store/gui'
    import type {Actions, Mutations} from '../../store/gui'
    import {useNamespacedActions, useNamespacedMutations} from 'vuex-composition-helpers'
    import type {BootstrapVariant} from '../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps<{bgVariant: BootstrapVariant, height: string, marginEnd?: string, marginTop?: string, width: string}>()
    const {[ActionTypes.DRAG]: drag} = useNamespacedActions<Actions>('gui', [ActionTypes.DRAG])
    const {[MutationTypes.ENABLE_DRAG]: enableDrag} = useNamespacedMutations<Mutations>('gui', [MutationTypes.ENABLE_DRAG])
</script>

<template>
    <AppShowGuiCard
        :bg-variant="bgVariant"
        :height="height"
        :margin-end="marginEnd"
        :margin-top="marginTop"
        :width="width">
        <hr class="resizer" @click="enableDrag" @mousedown="drag"/>
        <slot/>
    </AppShowGuiCard>
</template>
