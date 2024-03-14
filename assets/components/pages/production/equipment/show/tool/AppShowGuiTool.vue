<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppToolFormShow from './AppToolFormShow.vue'
    import {useRoute, useRouter} from 'vue-router'
    import {useToolsStore} from '../../../../../../stores/production/engine/tool/tools'
    import {onBeforeMount, ref} from 'vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppBtn from '../../../../../AppBtn.vue'
    import AppImg from '../../../../../AppImg.vue'
    // import AppShowComponentTabGeneral from '../../../../purchase/component/show/left/AppShowComponentTabGeneral.vue';
    // import AppComponentShowInlist from '../../../../purchase/component/show/AppComponentShowInlist.vue';
    // import AppComponentFormShow from '../../../../purchase/component/show/AppComponentFormShow.vue';
    import AppShowToolTabGeneral from './AppShowToolTabGeneral.vue'
    import AppWorkflowShow from '../../../../../workflow/AppWorkflowShow.vue'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const iriEngine = ref('')
    const beforeMountDataLoaded = ref(false)
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    const imageUpdateUrl = `/api/engines/${idEngine}/image`
    //region récupération information Outils
    const useFetchToolsStore = useToolsStore()
    useFetchToolsStore.fetchOne(idEngine)
    onBeforeMount(() => {
        const promises = []
        console.log('onBeforeMount')
        // promises.push(fetchUnits.fetchOp())
        promises.push(useFetchToolsStore.fetchOne(idEngine))
        Promise.all(promises).then(() => {
            iriEngine.value = useFetchToolsStore.engine['@id']
            beforeMountDataLoaded.value = true
        })
    })
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    //endregion
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchToolsStore.isLoaded = false
        promises.push(useFetchToolsStore.fetchOne(idEngine))
        // promises.push(fetchUnits.fetchOp())
        Promise.all(promises).then(() => {
            keyTitle.value++
        })
    }
    const onImageUpdate = () => {
        window.location.reload()
    }
    // //region récupéation information unité ??
    // const fetchUnits = useOptions('units')
    // fetchUnits.fetchOp()
    // //endregion
    const router = useRouter()
    function goBack() {
        router.push({name: 'tools'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <div class="d-flex flex-row">
                        <div>
                            <button class="text-dark mr-10" title="Retour à la liste des outils" @click="goBack">
                                <FontAwesomeIcon icon="toolbox"/> Outils
                            </button>
                            <b>{{ useFetchToolsStore.engine.code }}</b>: {{ useFetchToolsStore.engine.name }}
                        </div>
                        <AppSuspense>
                            <AppWorkflowShow :workflow-to-show="['engine', 'blocker']" :item-iri="iriEngine"/>
                        </AppSuspense>
                        <span class="ml-auto">
                            <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                            <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                        </span>
                    </div>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useFetchToolsStore.engine.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowToolTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppToolFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <!--   <AppComponentShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>-->
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

