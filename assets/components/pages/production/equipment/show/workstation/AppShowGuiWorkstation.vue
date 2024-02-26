<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import AppWorkstationFormShow from './AppWorkstationFormShow.vue'
    import {useRoute, useRouter} from 'vue-router'
    import {useWorkstationsStore} from '../../../../../../stores/production/engine/workstation/workstations'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppBtn from '../../../../../AppBtn.vue'
    import {ref} from 'vue'
    import AppImg from '../../../../../AppImg.vue'
    import AppShowWorkstationTabGeneral from './tabs/AppShowWorkstationTabGeneral.vue'
    // import AppShowComponentTabGeneral from '../../../../purchase/component/show/left/AppShowComponentTabGeneral.vue';
    // import AppComponentFormShow from '../../../../purchase/component/show/AppComponentFormShow.vue';
    // import AppComponentShowInlist from '../../../../purchase/component/show/AppComponentShowInlist.vue';
    import AppWorkstationShowInlist from './AppWorkstationShowInlist.vue'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const keyTitle = ref(0)
    const modeDetail = ref(true)
    //region récupération information Workstations
    const useFetchWorkstationsStore = useWorkstationsStore()
    const imageUpdateUrl = `/api/engines/${idEngine}/image`
    const isFullScreen = ref(false)
    useFetchWorkstationsStore.fetchOne(idEngine)
    const keyTabs = ref(0)
    //endregion
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchWorkstationsStore.isLoaded = false
        promises.push(useFetchWorkstationsStore.fetchOne(idEngine))
        // promises.push(fetchUnits.fetchOp())
        Promise.all(promises).then(() => {
            keyTitle.value++
        })
    }
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
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
        router.push({name: 'engines'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <!--                    <img src="/public/img/production/icons8-usine-48.png" alt="icône Workstation"/>-->
                    <button class="text-dark" @click="goBack">
                        <FontAwesomeIcon icon="oil-well"/>
                    </button>
                    <b>{{ useFetchWorkstationsStore.engine.code }}</b>: {{ useFetchWorkstationsStore.engine.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useFetchWorkstationsStore.engine.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowWorkstationTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppWorkstationFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppWorkstationShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
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

