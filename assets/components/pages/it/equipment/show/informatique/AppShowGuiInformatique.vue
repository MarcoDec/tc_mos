<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import useOptions from '../../../../../../stores/option/options'
    import {useRoute, useRouter} from 'vue-router'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeMount, ref} from 'vue'
    import AppImg from '../../../../../AppImg.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppShowInformatiqueTabGeneral from './AppShowInformatiqueTabGeneral.vue'
    import AppInformatiqueFormShow from './AppInformatiqueFormShow.vue'
    import {useInformatiqueStore} from '../../../../../../stores/it/equipment/informatique/informatique'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchUnits = useOptions('units')
    const useEngineStore = useInformatiqueStore()
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const modeDetail = ref(true)
    const beforeMountDataLoaded = ref(false)
    const isFullScreen = ref(false)
    const imageUpdateUrl = `/api/engines/${idEngine}/image`
    fetchUnits.fetchOp()

    onBeforeMount(() => {
        const promises = []
        // console.log('onBeforeMount')
        promises.push(fetchUnits.fetchOp())
        promises.push(useEngineStore.fetchOne(idEngine))
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
            // console.log('beforeMountDataLoaded', useEngineStore.engine)
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
        useEngineStore.isLoaded = false
        promises.push(useEngineStore.fetchOne(idEngine))
        promises.push(fetchUnits.fetchOp())
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

    const router = useRouter()
    function goBack() {
        router.push({name: 'informatiques'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <button class="text-dark mr-10" title="Retour à la liste des matériels informatiques" @click="goBack">
                        <FontAwesomeIcon icon="laptop-code"/> Matériel informatique
                    </button>
                    <b>{{ useEngineStore.engine.code }}</b>: {{ useEngineStore.engine.name }}
                    <!--    <span class="btn-float-right">-->
                    <!--        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>-->
                    <!--        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>-->
                    <!--    </span>-->
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="useEngineStore.engine.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowInformatiqueTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppInformatiqueFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <!-- <AppInformatiqueShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>-->
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

