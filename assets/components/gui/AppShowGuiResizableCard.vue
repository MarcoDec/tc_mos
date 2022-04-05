<script setup>
    import {useNamespacedActions, useNamespacedMutations} from 'vuex-composition-helpers'
    import AppShowGuiCard from './AppShowGuiCard.vue'
    import {defineProps} from 'vue'

    defineProps({
        bgVariant: {required: true, type: String},
        height: {required: true, type: String},
        marginEnd: {default: null, type: String},
        marginTop: {default: null, type: String},
        width: {required: true, type: String}
    })
    const drag = useNamespacedActions('gui', ['drag']).drag
    const enableDrag = useNamespacedMutations('gui', ['enableDrag']).enableDrag
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
