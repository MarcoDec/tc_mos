<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeUnmount, onBeforeMount, ref} from 'vue'
    import AppBtn from '../../../../AppBtn.vue'
    import AppImg from '../../../../AppImg.vue'
    import AppComponentFormShow from './AppComponentFormShow.vue'
    import AppShowGuiGen from '../../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import useOptions from '../../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import AppComponentShowInlist from './AppComponentShowInlist.vue'
    import AppShowComponentTabGeneral from './left/AppShowComponentTabGeneral.vue'

    //region définition des constantes
    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    const keyTabs = ref(0)
    const keyTitle = ref(0)
    const beforeMountDataLoaded = ref(false)
    //endregion
    //region Chargement des données
    onBeforeMount(() => {
        const promises = []
        console.log('onBeforeMount')
        promises.push(fetchUnits.fetchOp())
        promises.push(useFetchComponentStore.fetchOne(idComponent))
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    //endregion
    //region définition des méthodes
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const imageUpdateUrl = `/api/components/${idComponent}/image`
    //endregion
    //region déchargement des données
    onBeforeUnmount(() => {
        useFetchComponentStore.reset()
        fetchUnits.resetItems()
    })
    //endregion
    const onImageUpdate = () => {
        // console.log('onImageUpdate')
        useFetchComponentStore.fetchOne(idComponent)
    }
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchComponentStore.isLoaded = false
        promises.push(useFetchComponentStore.fetchOne(idComponent))
        promises.push(fetchUnits.fetchOp())
        Promise.all(promises).then(() => {
            keyTitle.value++
        })
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useFetchComponentStore.component.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowComponentTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppComponentFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppComponentShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
                        </AppSuspense>
                    </div>
                    <span>
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </span>
                </div>
            </template>
            <template #gui-right/>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
    .selected-detail {
        background-color: #46e046 !important;
        color: white !important;
        border: 1px solid #46e046;
    }
</style>
