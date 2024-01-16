<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeUnmount, ref} from 'vue'
    import AppBtn from '../../../AppBtn.vue'
    import AppImg from '../../../AppImg.vue'
    import AppComponentFormShow from './AppComponentFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../stores/purchase/component/components'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import AppComponentShowInlist from './AppComponentShowInlist.vue'
    import AppShowComponentTabGeneral from './tabs/AppShowComponentTabGeneral.vue'

    //region définition des constantes
    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    //endregion
    //region Chargement des données
    fetchUnits.fetchOp()
    useFetchComponentStore.fetchOne(idComponent)
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
    const imageUpdateUrl = '/api/components/' + idComponent + '/image'
    //endregion
    //region déchargement des données
    onBeforeUnmount(() => {
        useFetchComponentStore.reset()
        fetchUnits.resetItems()
    })
    //endregion
    const onImageUpdate = () => {
        useFetchComponentStore.fetchOne(idComponent)
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div class="bg-white border-1 border-dark p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        :file-path="useFetchComponentStore.component.filePath"
                        :image-update-url="imageUpdateUrl"
                        class="width30"
                    @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowComponentTabGeneral class="width70"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="full-visible-width font-small">
                    <div class="btn-container">
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </div>
                    <AppSuspense><AppComponentFormShow v-if="useFetchComponentStore.isLoaded && fetchUnits.isLoaded && modeDetail"/></AppSuspense>
                    <AppSuspense><AppComponentShowInlist v-if="!modeDetail"/></AppSuspense>
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
    .border-dark {
        border-bottom: 1px solid grey;
    }
    .full-visible-width {
        min-width:calc(100vw - 35px);
        padding: 2px;
    }
    .full-screen {
        position: fixed;
        top: 10px;
        left: 0;
        width: 95vw;
        height: 100vh;
        z-index: 10000; /* Assurez-vous que c'est au-dessus des autres éléments */
        background-color: white; /* ou toute autre couleur de fond souhaitée */
    }
    .btn-container {
        position: relative;
        float: right;
        background-color: white;
        top: 0;
        right: 0;
        z-index: 10010; /* Assurez qu'il reste au-dessus du contenu */
    }
</style>
