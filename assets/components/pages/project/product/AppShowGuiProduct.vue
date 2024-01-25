<script setup>
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {onBeforeMount, onBeforeUnmount, ref} from 'vue'
    import AppBtn from '../../../AppBtn.vue'
    import AppImg from '../../../AppImg.vue'
    import AppComponentFormShow from '../../purchase/component/show/AppComponentFormShow.vue'
    import AppComponentShowInlist from '../../purchase/component/show/AppComponentShowInlist.vue'
    import AppProductFormShow from './AppProductFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import useOptions from '../../../../stores/option/options'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import AppProductShowInlist from './AppProductShowInlist.vue'
    import AppShowProductTabGeneral from './tabs/AppShowProductTabGeneral.vue'
    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchUnits = useOptions('units')
    const useFetchProductStore = useProductStore()
    const beforeMountDataLoaded = ref(false)
    const modeDetail = ref(true)
    const imageUpdateUrl = `/api/products/${idProduct}/image`
    const isFullScreen = ref(false)
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
        // console.log('onImageUpdate')
        useFetchProductStore.fetchOne(idProduct)
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
</script>

<template>
    <AppShowGuiGen v-if="beforeMountDataLoaded">
        <template #gui-left>
            <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                <FontAwesomeIcon icon="fa-brands fa-product-hunt" />
                <b>{{ useFetchProductStore.product.code }}</b>: {{ useFetchProductStore.product.name }}
                <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
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

