<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import AppTestCounterPartFormShow from './AppTestCounterPartFormShow.vue'
    import {useCounterPartStore} from '../../../../../../stores/production/engine/test-counter-part/testCounterPart'
    import useOptions from '../../../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import AppBtn from "../../../../../AppBtn.vue";
    import {onBeforeMount, ref} from "vue";
    import AppImg from "../../../../../AppImg.vue";
    import AppShowComponentTabGeneral from "../../../../purchase/component/show/left/AppShowComponentTabGeneral.vue";
    import AppSuspense from "../../../../../AppSuspense.vue";
    import AppShowCounterPartTabGeneral from "./AppShowCounterPartTabGeneral.vue";
    import AppComponentShowInlist from "../../../../purchase/component/show/AppComponentShowInlist.vue";
    import AppComponentFormShow from "../../../../purchase/component/show/AppComponentFormShow.vue";

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchUnits = useOptions('units')
    const useFetchCounterPartStore = useCounterPartStore()
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const modeDetail = ref(true)
    const beforeMountDataLoaded = ref(false)
    const isFullScreen = ref(false)
    const imageUpdateUrl = `/api/engines/${idEngine}/image`
    fetchUnits.fetchOp()

    onBeforeMount(() => {
        const promises = []
        console.log('onBeforeMount')
        promises.push(fetchUnits.fetchOp())
        promises.push(useFetchCounterPartStore.fetchOne(idEngine))
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchCounterPartStore.isLoaded = false
        promises.push(useFetchCounterPartStore.fetchOne(idEngine))
        promises.push(fetchUnits.fetchOp())
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
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ useFetchCounterPartStore.engine.code }}</b>: {{ useFetchCounterPartStore.engine.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useFetchCounterPartStore.engine.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowCounterPartTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppTestCounterPartFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <!--  <AppComponentShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>-->
                        </AppSuspense>
                    </div>
                    <span>
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </span>
                </div>
                <!--            <AppTabs id="gui-bottom">-->
                <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
                <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
                <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
                <!--            </AppTabs>-->
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

