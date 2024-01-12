<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeUnmount, ref} from 'vue'
    import AppBtn from '../../../AppBtn.vue'
    import AppComponentFormShow from './AppComponentFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../stores/purchase/component/components'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {BImg} from 'bootstrap-vue-next'
    import AppComponentShowInlist from './AppComponentShowInlist.vue'
    import AppShowComponentTabGeneral from './tabs/AppShowComponentTabGeneral.vue'

    //region définition des constantes
    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    const modeDetail = ref(true)
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
    //endregion
    //region déchargement des données
    onBeforeUnmount(() => {
        useFetchComponentStore.reset()
        fetchUnits.resetItems()
    })
    //endregion
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div class="bg-white border-1 border-dark p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
                    <span class="btn-float-right">
                        <AppBtn label="Détails" icon="eye" @click="requestDetails"/>
                        <AppBtn label="Exploitation" icon="industry" variant="warning" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <div class="m-1" style="width:30%">
                        <BImg thumbnail fluid :src="useFetchComponentStore.component.filePath" alt="Image 1"></BImg>
                    </div>
                    <AppSuspense><AppShowComponentTabGeneral style="width:70%"/></AppSuspense>
                </div>

            </template>
            <template #gui-bottom>
                <div class="full-visible-width font-small">
                    <AppSuspense><AppComponentFormShow v-if="useFetchComponentStore.isLoaded && fetchUnits.isLoaded && modeDetail"/></AppSuspense>
                    <AppSuspense><AppComponentShowInlist v-if="!modeDetail"/></AppSuspense>
                </div>
            </template>
            <template #gui-right/>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
    .full-visible-width {
        min-width:calc(100vw - 35px);
        padding: 2px;
    }
</style>
