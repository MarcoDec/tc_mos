<script setup>
    import AppBtn from '../../../AppBtn.vue'
    import AppImg from '../../../AppImg.vue'
    import AppProductFormShow from './AppProductFormShow.vue'
    import AppProductShowInlist from './AppProductShowInlist.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppShowProductTabGeneral from './tabs/AppShowProductTabGeneral.vue'
    import useOptions from '../../../../stores/option/options'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeMount, onBeforeUnmount, ref} from 'vue'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute, useRouter} from 'vue-router'
    import AppWorkflowShow from "../../../workflow/AppWorkflowShow.vue"
    import AppSuspense from "../../../AppSuspense.vue"

    const router = useRouter()
    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const iriProduct = `/api/products/${idProduct}`
    const fetchUnits = useOptions('units')
    const useFetchProductStore = useProductStore()
    const beforeMountDataLoaded = ref(false)
    const modeDetail = ref(true)
    const imageUpdateUrl = `/api/products/${idProduct}/image`
    const isFullScreen = ref(false)
    const keyTabs = ref(0)
    const keyTitle = ref(0)
    onBeforeMount(() => {
        const promises = []
        promises.push(fetchUnits.fetchOp())
        promises.push(useFetchProductStore.fetchOne(idProduct))
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    onBeforeUnmount(() => {
        useFetchProductStore.reset()
    })
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    const onImageUpdate = () => {
        window.location.reload()
    }
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        useFetchProductStore.isLoaded = false
        promises.push(useFetchProductStore.fetchOne(idProduct))
        promises.push(fetchUnits.fetchOp())
        Promise.all(promises).then(() => {
            keyTitle.value++
        })
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    function goToTheList() {
        router.push({name: 'product-list'})
    }
</script>

<template>
    <AppShowGuiGen v-if="beforeMountDataLoaded">
        <template #gui-left>
            <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                <div class="d-flex flex-row">
                    <button class="text-dark" @click="goToTheList">
                        <FontAwesomeIcon icon="fa-brands fa-product-hunt"/>
                    </button>
                    <b>{{ useFetchProductStore.product.code }}</b>: {{ useFetchProductStore.product.name }}
                    <AppSuspense>
                        <AppWorkflowShow :workflow-to-show="['product', 'blocker']" :item-iri="iriProduct"/>
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
                    :file-path="useFetchProductStore.product.filePath"
                    :image-update-url="imageUpdateUrl"
                    @update:file-path="onImageUpdate"/>
                <AppSuspense><AppShowProductTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
            </div>
        </template>
        <template #gui-bottom>
            <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                <div class="full-visible-width">
                    <AppSuspense>
                        <AppProductFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                        <AppProductShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
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
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>

