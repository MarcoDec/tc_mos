<script setup>
    import AppShowGuiGen from '../../../AppShowGuiGen.vue'
    import AppSupplierFormShow from './AppSupplierFormShow.vue'
    import AppSupplierShowInlist from './bottom/AppSupplierShowInlist.vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppImg from '../../../../AppImg.vue'
    import AppBtn from '../../../../AppBtn.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import {onBeforeMount, ref} from 'vue'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import {useRoute} from 'vue-router'
    import AppSupplierShowTabGeneral from './tabs/AppSupplierShowTabGeneral.vue'

    const isFullScreen = ref(false)

    const beforeMountDataLoaded = ref(false)
    const keyTitle = ref(0)
    const fetchSupplierStore = useSuppliersStore()
    const route = useRoute()
    const idSupplier = Number(route.params.id_supplier)
    const keyTabs = ref(0)
    const modeDetail = ref(true)
    const imageUpdateUrl = `/api/suppliers/${idSupplier}/image`

    onBeforeMount(() => {
        fetchSupplierStore.fetchOne(idSupplier).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    const onUpdated = () => {
        keyTitle.value++
    }
    const onImageUpdate = () => {
        fetchSupplierStore.fetchOne(idSupplier).then(() => {
            keyTitle.value++
            keyTabs.value++
        })
    }
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
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <FontAwesomeIcon icon="user-tie"/>
                    <b>{{ fetchSupplierStore.supplier.id }}</b>: {{ fetchSupplierStore.supplier.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        :key="`img-${keyTabs}`"
                        class="width30"
                        :file-path="fetchSupplierStore.supplier.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense>
                        <AppSupplierShowTabGeneral
                            :key="`form-${keyTabs}`"
                            class="width70"
                            :data-customers="fetchSupplierStore.supplier"
                            @updated="onUpdated"/>
                    </AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppSupplierFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppSupplierShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
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
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
