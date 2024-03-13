<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import {useRoute, useRouter} from 'vue-router'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {ref} from 'vue'
    import AppImg from '../../../../../AppImg.vue'
    import {useGenEngineStore} from '../../../../../../stores/production/engine/generic/engines'
    import AppShowMachineTabGeneral from './tabs/AppShowMachineTabGeneral.vue'
    import AppMachineFormShow from './AppMachineFormShow.vue'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const keyTitle = ref(0)
    const modeDetail = ref(true)
    //region récupération information Workstations
    const useFetchEnginesStore = useGenEngineStore('machines', '/api/machines')
    const imageUpdateUrl = `/api/engines/${idEngine}/image`
    const isFullScreen = ref(false)
    useFetchEnginesStore().fetchOne(idEngine)
    const keyTabs = ref(0)
    //endregion
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchEnginesStore().isLoaded = false
        promises.push(useFetchEnginesStore().fetchOne(idEngine))
        // promises.push(fetchUnits.fetchOp())
        Promise.all(promises).then(() => {
            keyTitle.value++
        })
    }
    // const requestDetails = () => {
    //     modeDetail.value = true
    // }
    // const requestExploitation = () => {
    //     modeDetail.value = false
    // }
    const onImageUpdate = () => {
        window.location.reload()
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const router = useRouter()
    function goBack() {
        router.push({name: 'machines'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <button class="text-dark mr-10" title="Retour à la liste des machines" @click="goBack">
                        <FontAwesomeIcon icon="cogs"/> Machine
                    </button>
                    <b>{{ useFetchEnginesStore().engine.code }}</b>: {{ useFetchEnginesStore().engine.name }}
                    <!--                    <span class="btn-float-right">-->
                    <!--                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>-->
                    <!--                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>-->
                    <!--                    </span>-->
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useFetchEnginesStore().engine.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowMachineTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppMachineFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <!-- <AppWorkstationShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>-->
                        </AppSuspense>
                    </div>
                    <span>
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </span>
                </div>
            </template>
            <template #gui-right>
                <!--            {{ route.params.id_product }}-->
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>

