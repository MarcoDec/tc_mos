<script setup>
    import {useRepo, useRouter} from '../../composition'
    import AppShowGuiCard from './AppShowGuiCard.vue'
    import {GuiRepository} from '../../store/modules'
    import {defineProps} from 'vue'

    defineProps({
        height: {required: true, type: String},
        marginEnd: {default: null, type: String},
        marginTop: {default: null, type: String},
        variant: {required: true, type: String},
        width: {required: true, type: String}
    })

    const {id} = useRouter()
    const repo = useRepo(GuiRepository)

    function drag() {
        repo.drag(id)
    }

    function enableDrag() {
        repo.enableDrag(id)
    }
</script>

<template>
    <AppShowGuiCard
        :height="height"
        :margin-end="marginEnd"
        :margin-top="marginTop"
        :variant="variant"
        :width="width">
        <hr class="resizer" @click="enableDrag" @mousedown="drag"/>
        <slot/>
    </AppShowGuiCard>
</template>
